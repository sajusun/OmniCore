<?php

declare(strict_types=1);

namespace App\Modules\AdaptiveAuth\Services;

use App\Modules\AdaptiveAuth\Mail\AdaptiveOtpMail;
use App\Modules\AdaptiveAuth\Mail\NewDeviceAlertMail;
use App\Modules\AdaptiveAuth\Models\DeviceLoginChallenge;
use App\Modules\AdaptiveAuth\Models\DeviceLoginLog;
use App\Modules\AdaptiveAuth\Models\UserDevice;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Cookie as HttpCookie;

class AdaptiveAuthService
{
    public function __construct(
        protected DeviceDetectorService $detector
    ) {
    }

    /**
     * Get the device detector service instance.
     */
    public function getDetector(): DeviceDetectorService
    {
        return $this->detector;
    }

    /**
     * Evaluate the risk of an authentication attempt.
     *
     * @param Model $user
     * @param Request $request
     * @return array
     */
    public function evaluateEnvironment(Model $user, Request $request, bool $isLoginAttempt = false): array
    {
        if (!config('adaptive_auth.enabled', true)) {
            return [
                'status'  => 'trusted',
                'bypass'  => true,
                'message' => 'Adaptive auth is globally disabled.',
            ];
        }

        $info = $this->detector->inspect($request);

        // 1. Check if device exists
        $device = UserDevice::where('authenticatable_type', $user->getMorphClass())
            ->where('authenticatable_id', $user->getKey())
            ->where('device_uuid', $info['device_uuid'])
            ->first();

        // If device was explicitly revoked by user
        if ($device && (!$device->is_trusted || $device->revoked_at !== null)) {
            if ($isLoginAttempt) {
                return $this->requireChallenge($user, $info, 'Previously revoked device attempting re-authentication');
            }

            return [
                'status'  => 'revoked',
                'device'  => $device,
                'message' => 'This device access has been revoked.',
            ];
        }

        if ($device && $device->isCurrentlyTrusted()) {
            // Check location strictness policy
            $geoPolicy = config('adaptive_auth.geo_check_level', 'city');

            if ($geoPolicy === 'strict_ip' && $device->last_ip !== $info['ip']) {
                return $this->requireChallenge($user, $info, 'IP address changed under strict IP policy');
            }

            if (in_array($geoPolicy, ['city', 'country'], true)) {
                // If country changed significantly (impossible travel or unexpected relocation)
                if ($device->country && $info['country'] && strcasecmp($device->country, $info['country']) !== 0) {
                    return $this->requireChallenge($user, $info, "Country anomaly detected: {$info['country']} vs {$device->country}");
                }
            }

            // All checks passed! Update activity timestamp & IP (throttled)
            if (!$device->last_active_at || $device->last_active_at->diffInMinutes(now()) >= 5) {
                $device->touchActivity($info['ip'], $info['city'], $info['country']);
            }

            // Record trusted login audit log ONLY if it is an explicit login attempt
            if ($isLoginAttempt) {
                $this->logActivity($user, $device, $info, 'trusted_login');
            }

            return [
                'status' => 'trusted',
                'device' => $device,
                'cookie' => $this->createDeviceCookie($device->device_uuid),
            ];
        }

        // 2. Check if this is the user's first login and auto-trust is enabled
        $hasAnyTrustedDevice = UserDevice::where('authenticatable_type', $user->getMorphClass())
            ->where('authenticatable_id', $user->getKey())
            ->trusted()
            ->exists();

        if (!$hasAnyTrustedDevice && config('adaptive_auth.trust_first_login', false)) {
            $newDevice = $this->registerTrustedDevice($user, $info);
            $this->logActivity($user, $newDevice, $info, 'trusted_login', 'First device bootstrapped as trusted');

            return [
                'status' => 'trusted',
                'device' => $newDevice,
                'cookie' => $this->createDeviceCookie($newDevice->device_uuid),
            ];
        }

        // 3. New / Unrecognized environment: Challenge required!
        return $this->requireChallenge($user, $info, 'New device or unrecognized environment');
    }

    /**
     * Bootstrap the current registration / verification environment as the user's primary trusted device.
     */
    public function bootstrapRegistrationDevice(Model $user, Request $request): array
    {
        $info = $this->detector->inspect($request);

        $device = $this->registerTrustedDevice($user, $info);

        $this->logActivity(
            $user,
            $device,
            $info,
            'trusted_login',
            'Primary device registered during account registration / verification'
        );

        $cookie = $this->createDeviceCookie($device->device_uuid);

        if ($request->hasSession()) {
            $request->session()->put('adaptive_device_uuid', $device->device_uuid);
        }

        return [
            'device' => $device,
            'cookie' => $cookie,
        ];
    }

    /**
     * Create and dispatch a new login challenge with email OTP.
     *
     * @param Model $user
     * @param array $deviceMetadata
     * @param bool $rememberDevice
     * @return DeviceLoginChallenge
     */
    public function createChallenge(Model $user, array $deviceMetadata, bool $rememberDevice = true): DeviceLoginChallenge
    {
        $otpLength   = (int) config('adaptive_auth.otp.length', 6);
        $expiryMins  = (int) config('adaptive_auth.otp.expires_minutes', 10);
        $maxAttempts = (int) config('adaptive_auth.otp.max_attempts', 3);
        $cooldownSec = (int) config('adaptive_auth.otp.resend_cooldown_seconds', 60);

        // Generate cryptographically secure numeric OTP
        $min = (int) pow(10, $otpLength - 1);
        $max = (int) pow(10, $otpLength) - 1;
        $otpCode = (string) random_int($min, $max);

        // Delete any expired pending challenges for this user/device
        DeviceLoginChallenge::where('authenticatable_type', $user->getMorphClass())
            ->where('authenticatable_id', $user->getKey())
            ->where('expires_at', '<', now())
            ->delete();

        $challenge = DeviceLoginChallenge::create([
            'challenge_token'     => (string) Str::uuid(),
            'authenticatable_type'=> $user->getMorphClass(),
            'authenticatable_id'  => $user->getKey(),
            'device_uuid'         => $deviceMetadata['device_uuid'],
            'device_metadata'     => $deviceMetadata,
            'otp_code_hash'       => Hash::make($otpCode),
            'attempts'            => 0,
            'max_attempts'        => $maxAttempts,
            'resend_available_at' => now()->addSeconds($cooldownSec),
            'expires_at'          => now()->addMinutes($expiryMins),
            'remember_device'     => $rememberDevice,
        ]);

        $this->logActivity($user, null, $deviceMetadata, 'challenge_issued');

        // Dispatch Email with OTP
        try {
            Mail::to($user->email)->send(
                new AdaptiveOtpMail($otpCode, $deviceMetadata, $expiryMins)
            );
        } catch (\Throwable $e) {
            Log::error('Failed to send Adaptive OTP Email: ' . $e->getMessage());
        }

        return $challenge;
    }

    /**
     * Verify submitted OTP against challenge token.
     *
     * @param string $challengeToken
     * @param string $otp
     * @return array
     */
    public function verifyChallenge(string $challengeToken, string $otp): array
    {
        $challenge = DeviceLoginChallenge::where('challenge_token', $challengeToken)->first();

        if (!$challenge) {
            return [
                'success' => false,
                'code'    => 'INVALID_CHALLENGE',
                'message' => 'The verification session could not be found or has expired.',
            ];
        }

        if ($challenge->isVerified()) {
            return [
                'success' => false,
                'code'    => 'ALREADY_VERIFIED',
                'message' => 'This verification code was already consumed.',
            ];
        }

        if ($challenge->isExpired()) {
            return [
                'success' => false,
                'code'    => 'EXPIRED',
                'message' => 'This verification code has expired. Please request a new one.',
            ];
        }

        if ($challenge->hasExceededAttempts()) {
            return [
                'success' => false,
                'code'    => 'MAX_ATTEMPTS_EXCEEDED',
                'message' => 'Maximum verification attempts exceeded. Please request a fresh code.',
            ];
        }

        // Verify OTP Hash
        if (!$challenge->verifyOtp(trim($otp))) {
            $challenge->incrementAttempts();
            $attemptsRemaining = max(0, $challenge->max_attempts - $challenge->attempts);

            $this->logActivity(
                $challenge->authenticatable,
                null,
                $challenge->device_metadata ?? [],
                'challenge_failed',
                "Wrong OTP code entered. Attempts remaining: {$attemptsRemaining}"
            );

            return [
                'success'            => false,
                'code'               => 'INVALID_OTP',
                'attempts_remaining' => $attemptsRemaining,
                'message'            => $attemptsRemaining > 0
                    ? "Invalid code. You have {$attemptsRemaining} attempt(s) remaining."
                    : 'Maximum attempts exceeded. Please request a new verification code.',
            ];
        }

        // Valid OTP confirmed!
        $challenge->markVerified();
        $user = $challenge->authenticatable;
        $meta = $challenge->device_metadata ?? [];

        // Register or mark device as trusted
        $device = $this->registerTrustedDevice($user, $meta);

        $this->logActivity($user, $device, $meta, 'challenge_passed');

        // Send New Device Login Security Alert Email if enabled
        if (config('adaptive_auth.notify_on_new_device', true)) {
            try {
                Mail::to($user->email)->send(
                    new NewDeviceAlertMail($device, $user->name ?? 'User')
                );
            } catch (\Throwable $e) {
                Log::warning('Failed to dispatch New Device Alert email: ' . $e->getMessage());
            }
        }

        return [
            'success' => true,
            'user'    => $user,
            'device'  => $device,
            'cookie'  => $this->createDeviceCookie($device->device_uuid),
            'message' => 'Device verified successfully.',
        ];
    }

    /**
     * Resend a fresh OTP for an active challenge session.
     *
     * @param string $challengeToken
     * @return array
     */
    public function resendOtp(string $challengeToken): array
    {
        $challenge = DeviceLoginChallenge::where('challenge_token', $challengeToken)->first();

        if (!$challenge) {
            return [
                'success' => false,
                'message' => 'Verification session not found.',
            ];
        }

        if (!$challenge->canResend()) {
            $secondsRemaining = now()->diffInSeconds($challenge->resend_available_at, false);
            return [
                'success'           => false,
                'seconds_remaining' => max(1, (int) $secondsRemaining),
                'message'           => "Please wait {$secondsRemaining} seconds before requesting another code.",
            ];
        }

        $otpLength   = (int) config('adaptive_auth.otp.length', 6);
        $expiryMins  = (int) config('adaptive_auth.otp.expires_minutes', 10);
        $cooldownSec = (int) config('adaptive_auth.otp.resend_cooldown_seconds', 60);

        $min = (int) pow(10, $otpLength - 1);
        $max = (int) pow(10, $otpLength) - 1;
        $newOtp = (string) random_int($min, $max);

        $challenge->update([
            'otp_code_hash'       => Hash::make($newOtp),
            'attempts'            => 0,
            'resend_available_at' => now()->addSeconds($cooldownSec),
            'expires_at'          => now()->addMinutes($expiryMins),
        ]);

        $user = $challenge->authenticatable;
        $meta = $challenge->device_metadata ?? [];

        try {
            Mail::to($user->email)->send(
                new AdaptiveOtpMail($newOtp, $meta, $expiryMins)
            );
        } catch (\Throwable $e) {
            Log::error('Failed to resend Adaptive OTP: ' . $e->getMessage());
        }

        return [
            'success' => true,
            'message' => 'A new 6-digit verification code has been sent to your email.',
        ];
    }

    /**
     * Helper to return challenge requirement.
     */
    protected function requireChallenge(Model $user, array $deviceMetadata, string $reason): array
    {
        return [
            'status'   => 'challenge_required',
            'user'     => $user,
            'metadata' => $deviceMetadata,
            'reason'   => $reason,
        ];
    }

    /**
     * Register or update a device record and mark it as trusted.
     */
    public function registerTrustedDevice(Model $user, array $meta): UserDevice
    {
        $trustDays = (int) config('adaptive_auth.trust_duration_days', 60);

        return UserDevice::updateOrCreate(
            [
                'authenticatable_type' => $user->getMorphClass(),
                'authenticatable_id'   => $user->getKey(),
                'device_uuid'          => $meta['device_uuid'],
            ],
            [
                'device_name'      => $meta['device_name'] ?? 'Unknown Device',
                'platform'         => $meta['platform'] ?? null,
                'browser'          => $meta['browser'] ?? null,
                'device_type'      => $meta['device_type'] ?? 'desktop',
                'fingerprint_hash' => $meta['fingerprint_hash'] ?? null,
                'last_ip'          => $meta['ip'] ?? '0.0.0.0',
                'city'             => $meta['city'] ?? null,
                'region'           => $meta['region'] ?? null,
                'country'          => $meta['country'] ?? null,
                'country_code'     => $meta['country_code'] ?? null,
                'is_trusted'       => true,
                'trusted_at'       => now(),
                'trusted_until'    => now()->addDays($trustDays),
                'last_active_at'   => now(),
                'revoked_at'       => null,
            ]
        );
    }

    /**
     * Create secure HTTP-only cookie with device UUID.
     */
    public function createDeviceCookie(string $deviceUuid): HttpCookie
    {
        $cookieName  = config('adaptive_auth.cookie_name', 'adaptive_device_token');
        $trustDays   = (int) config('adaptive_auth.trust_duration_days', 60);
        $minutes     = $trustDays * 24 * 60;

        return Cookie::make(
            name: $cookieName,
            value: $deviceUuid,
            minutes: $minutes,
            path: '/',
            domain: null,
            secure: request()->isSecure(),
            httpOnly: true,
            raw: false,
            sameSite: 'lax'
        );
    }

    /**
     * Log device activity.
     */
    protected function logActivity(
        Model $user,
        ?UserDevice $device,
        array $meta,
        string $status,
        ?string $failureReason = null
    ): void {
        try {
            $locationStr = trim(($meta['city'] ?? '') . ', ' . ($meta['country'] ?? ''), ', ');

            DeviceLoginLog::create([
                'authenticatable_type' => $user->getMorphClass(),
                'authenticatable_id'   => $user->getKey(),
                'device_id'            => $device?->id,
                'ip_address'           => $meta['ip'] ?? '0.0.0.0',
                'location'             => $locationStr ?: null,
                'device_name'          => $meta['device_name'] ?? null,
                'user_agent'           => $meta['user_agent'] ?? null,
                'status'               => $status,
                'failure_reason'       => $failureReason,
                'created_at'           => now(),
            ]);
        } catch (\Throwable $e) {
            // Avoid failing the auth flow if audit logging hits an error
            Log::warning('Failed to log device activity: ' . $e->getMessage());
        }
    }
}
