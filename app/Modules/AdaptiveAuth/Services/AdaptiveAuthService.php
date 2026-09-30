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
        protected DeviceDetectorService $detector,
        protected TotpService $totp
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
     * Get the TOTP service instance.
     */
    public function getTotp(): TotpService
    {
        return $this->totp;
    }

    /**
     * Determine if mandatory TOTP is required for the user based on role policy or user setup.
     */
    public function isTotpRequiredForUser(Model $user): bool
    {
        // Check if user explicitly enabled TOTP
        if ($this->totp->hasTotpEnabled($user)) {
            return true;
        }

        // Check role-based mandatory policy
        $role = strtolower((string) ($user->role ?? $user->type ?? 'customer'));
        $policies = config('adaptive_auth.role_policies', []);

        if (isset($policies[$role]) && ($policies[$role]['mode'] ?? '') === 'totp_mandatory') {
            return true;
        }

        return false;
    }

    /**
     * Evaluate the risk of an authentication attempt.
     *
     * @param Model $user
     * @param Request $request
     * @param bool $isLoginAttempt
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

        // Check if user requires TOTP MFA first
        if ($this->isTotpRequiredForUser($user)) {
            return [
                'status'        => 'totp_required',
                'totp_required' => true,
                'message'       => 'Authenticator TOTP code required.',
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
            $geoPolicy = config('adaptive_auth.geo_check_level', 'country');

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
            if (!empty($user->email)) {
                Mail::to($user->email)->send(
                    new AdaptiveOtpMail(
                        otpCode: $otpCode,
                        deviceInfo: $deviceMetadata,
                        expiresInMinutes: $expiryMins
                    )
                );
            }
        } catch (\Throwable $e) {
            Log::error('AdaptiveAuth: Failed to dispatch OTP email: ' . $e->getMessage());
        }

        return $challenge;
    }

    /**
     * Verify an OTP challenge submission.
     */
    public function verifyChallenge(string $challengeToken, string $submittedOtp): array
    {
        /** @var DeviceLoginChallenge|null $challenge */
        $challenge = DeviceLoginChallenge::where('challenge_token', $challengeToken)->first();

        if (!$challenge) {
            return [
                'success' => false,
                'status'  => 'invalid_token',
                'message' => 'The verification session is invalid or has expired. Please sign in again.',
            ];
        }

        $user = $challenge->authenticatable;
        $meta = $challenge->device_metadata ?? [];

        if (!$user) {
            $challenge->delete();
            return [
                'success' => false,
                'status'  => 'user_not_found',
                'message' => 'User associated with this challenge could not be found.',
            ];
        }

        if ($challenge->isExpired()) {
            $this->logActivity($user, null, $meta, 'challenge_failed', 'OTP expired');
            $challenge->delete();

            return [
                'success' => false,
                'status'  => 'expired',
                'message' => 'This verification code has expired. Please request a new code.',
            ];
        }

        if ($challenge->isLocked()) {
            $this->logActivity($user, null, $meta, 'challenge_failed', 'Max attempts exceeded');
            $challenge->delete();

            return [
                'success' => false,
                'status'  => 'locked',
                'message' => 'Too many failed verification attempts. Please sign in again to receive a fresh code.',
            ];
        }

        // Clean user input
        $cleanSubmittedOtp = preg_replace('/\s+/', '', $submittedOtp);

        if (!Hash::check($cleanSubmittedOtp, $challenge->otp_code_hash)) {
            $challenge->incrementAttempts();
            $remaining = $challenge->getRemainingAttempts();

            $this->logActivity($user, null, $meta, 'challenge_failed', "Invalid OTP entered ({$remaining} attempts left)");

            return [
                'success'            => false,
                'status'             => 'invalid_otp',
                'code'               => 'INVALID_OTP',
                'remaining_attempts' => $remaining,
                'attempts_remaining' => $remaining,
                'message'            => $remaining > 0
                    ? "Incorrect verification code. You have {$remaining} attempt(s) remaining."
                    : 'Incorrect code. Maximum attempts exceeded. Please sign in again.',
            ];
        }

        // OTP Verified Successfully!
        $challenge->markAsVerified();

        // Register or trust the device
        $device = $this->registerTrustedDevice($user, $meta);

        // Clean up completed challenge record
        $challenge->delete();

        $this->logActivity($user, $device, $meta, 'trusted_login', 'New device verified via OTP');

        // Send alert email about newly verified device
        if (config('adaptive_auth.notify_on_new_device', true) && !empty($user->email)) {
            try {
                Mail::to($user->email)->send(
                    new NewDeviceAlertMail(
                        device: $device,
                        userName: $user->name ?? 'User'
                    )
                );
            } catch (\Throwable $e) {
                Log::warning('AdaptiveAuth: Failed to send new device alert email: ' . $e->getMessage());
            }
        }

        $cookie = $this->createDeviceCookie($device->device_uuid);

        return [
            'success' => true,
            'status'  => 'verified',
            'user'    => $user,
            'device'  => $device,
            'cookie'  => $cookie,
            'message' => 'Device verified successfully.',
        ];
    }

    /**
     * Resend a fresh OTP code for an active challenge.
     */
    public function resendOtp(string $challengeToken): array
    {
        /** @var DeviceLoginChallenge|null $challenge */
        $challenge = DeviceLoginChallenge::where('challenge_token', $challengeToken)->first();

        if (!$challenge) {
            return [
                'success' => false,
                'status'  => 'invalid_token',
                'message' => 'The verification session has expired. Please sign in again.',
            ];
        }

        if (!$challenge->canResend()) {
            return [
                'success'          => false,
                'status'           => 'cooldown_active',
                'cooldown_seconds' => $challenge->getCooldownRemainingSeconds(),
                'message'          => "Please wait {$challenge->getCooldownRemainingSeconds()} seconds before requesting another code.",
            ];
        }

        $user = $challenge->authenticatable;
        $meta = $challenge->device_metadata ?? [];

        $otpLength   = (int) config('adaptive_auth.otp.length', 6);
        $expiryMins  = (int) config('adaptive_auth.otp.expires_minutes', 10);
        $cooldownSec = (int) config('adaptive_auth.otp.resend_cooldown_seconds', 60);

        $min = (int) pow(10, $otpLength - 1);
        $max = (int) pow(10, $otpLength) - 1;
        $newOtpCode = (string) random_int($min, $max);

        $challenge->update([
            'otp_code_hash'       => Hash::make($newOtpCode),
            'attempts'            => 0,
            'resend_available_at' => now()->addSeconds($cooldownSec),
            'expires_at'          => now()->addMinutes($expiryMins),
        ]);

        try {
            if ($user && !empty($user->email)) {
                Mail::to($user->email)->send(
                    new AdaptiveOtpMail(
                        otpCode: $newOtpCode,
                        deviceInfo: $meta,
                        expiresInMinutes: $expiryMins
                    )
                );
            }
        } catch (\Throwable $e) {
            Log::error('AdaptiveAuth: Failed to resend OTP email: ' . $e->getMessage());
        }

        return [
            'success'          => true,
            'status'           => 'resent',
            'cooldown_seconds' => $cooldownSec,
            'message'          => 'A fresh verification code has been sent to your email address.',
        ];
    }

    /**
     * Require a challenge for an unrecognized or risky session.
     */
    protected function requireChallenge(Model $user, array $meta, string $reason): array
    {
        return [
            'status'   => 'challenge_required',
            'reason'   => $reason,
            'metadata' => $meta,
            'message'  => 'Additional verification required for this device.',
        ];
    }

    /**
     * Register or update a trusted device for a user.
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
