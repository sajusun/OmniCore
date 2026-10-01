<?php

declare(strict_types=1);

namespace App\Modules\AdaptiveAuth\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\AdaptiveAuth\Models\DeviceLoginChallenge;
use App\Modules\AdaptiveAuth\Services\AdaptiveAuthService;
use App\Modules\AdaptiveAuth\Services\TotpService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdaptiveAuthApiController extends Controller
{
    public function __construct(
        protected AdaptiveAuthService $adaptiveAuth,
        protected TotpService $totp
    ) {
        parent::__construct();
    }

    /**
     * Complete email OTP adaptive challenge via API.
     */
    public function verify(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'challenge_token' => 'required|string',
                'otp'             => 'required|string|min:4|max:8',
            ]);

            if ($validator->fails()) {
                return $this->error($validator->errors()->first(), $validator->errors(), 422);
            }

            $result = $this->adaptiveAuth->verifyChallenge(
                $request->input('challenge_token'),
                $request->input('otp')
            );

            if (!$result['success']) {
                $status = ($result['code'] ?? '') === 'INVALID_CHALLENGE' ? 404 : 400;
                return $this->error($result['message'], $result, $status);
            }

            $user = $result['user'];

            // Generate Token (Supports JWT auth or Sanctum)
            $token = null;
            $tokenType = 'bearer';

            if (auth('api')->check() || method_exists(auth('api'), 'login')) {
                try {
                    $token = auth('api')->login($user);
                } catch (\Throwable $e) {}
            }

            if (!$token && method_exists($user, 'createToken')) {
                $token = $user->createToken('AdaptiveAuthDevice')->plainTextToken;
            }

            $response = $this->success([
                'token_type'   => $tokenType,
                'token'        => $token,
                'user'         => $user->only(['id', 'name', 'email', 'avatar']),
                'device'       => [
                    'device_name' => $result['device']->device_name,
                    'is_trusted'  => $result['device']->is_trusted,
                    'trusted_at'  => $result['device']->trusted_at,
                    'ip'          => $result['device']->last_ip,
                    'location'    => trim(($result['device']->city ?? '') . ', ' . ($result['device']->country ?? ''), ', '),
                ],
            ], 'Device verified and login successful.');

            // Attach secure device cookie to response
            return $response->withCookie($result['cookie']);
        } catch (Exception $e) {
            return $this->error('Failed to process verification challenge.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Complete TOTP 2FA challenge via API during login.
     */
    public function verifyTotp(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'challenge_token' => 'required|string',
                'code'            => 'required|string',
                'remember_device' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return $this->error($validator->errors()->first(), $validator->errors(), 422);
            }

            $challenge = DeviceLoginChallenge::where('challenge_token', $request->input('challenge_token'))->first();

            if (!$challenge || $challenge->isExpired() || $challenge->isVerified()) {
                return $this->error('Verification session expired or invalid. Please sign in again.', [], 404);
            }

            $user = $challenge->authenticatable;
            $code = (string) $request->input('code');

            if (!$this->totp->verifyUserTotpOrRecovery($user, $code)) {
                return $this->error('Invalid Authenticator code. Please check your authenticator app and try again.', [], 422);
            }

            // Mark challenge verified
            $challenge->update(['verified_at' => now()]);

            $deviceMeta = $challenge->device_metadata ?? [];
            $cookie = null;
            $device = null;

            if ($request->boolean('remember_device', true)) {
                $device = $this->adaptiveAuth->registerTrustedDevice($user, $deviceMeta);
                $cookie = $this->adaptiveAuth->createDeviceCookie($device->device_uuid);
                $this->adaptiveAuth->logActivity($user, $device, $deviceMeta, 'trusted_login', 'API device verified via TOTP');
            } else {
                $this->adaptiveAuth->logActivity($user, null, $deviceMeta, 'trusted_login', 'Temporary API login via TOTP (Not remembered)');
            }

            // Generate Token
            $token = null;
            $tokenType = 'bearer';

            if (auth('api')->check() || method_exists(auth('api'), 'login')) {
                try {
                    $token = auth('api')->login($user);
                } catch (\Throwable $e) {}
            }

            if (!$token && method_exists($user, 'createToken')) {
                $token = $user->createToken('AdaptiveAuthDevice')->plainTextToken;
            }

            $response = $this->success([
                'token_type'   => $tokenType,
                'token'        => $token,
                'user'         => $user->only(['id', 'name', 'email', 'avatar']),
                'device'       => $device ? [
                    'device_name' => $device->device_name,
                    'is_trusted'  => $device->is_trusted,
                    'ip'          => $device->last_ip,
                ] : null,
            ], 'Two-Factor Authentication successful. Login granted.');

            if ($cookie) {
                $response->withCookie($cookie);
            }

            return $response;
        } catch (Exception $e) {
            return $this->error('Failed to verify TOTP code.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Resend verification OTP code via API.
     */
    public function resend(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'challenge_token' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->error($validator->errors()->first(), $validator->errors(), 422);
            }

            $result = $this->adaptiveAuth->resendOtp($request->input('challenge_token'));

            if (!$result['success']) {
                return $this->error($result['message'], $result, 400);
            }

            return $this->success($result, $result['message']);
        } catch (Exception $e) {
            return $this->error('Failed to resend code.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Initialize TOTP setup (generates secret key and QR code data).
     */
    public function totpSetup(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $secretKey = $this->totp->generateSecretKey();
            $otpUrl = $this->totp->getOtpAuthUrl($user, $secretKey);

            return $this->success([
                'secret_key'              => $secretKey,
                'otp_auth_url'            => $otpUrl,
                'is_enabled'              => $this->totp->hasTotpEnabled($user),
                'always_require_on_login' => $this->totp->alwaysRequiresTotpOnLogin($user),
                'recovery_codes_count'    => $this->totp->getRemainingRecoveryCodesCount($user),
            ], 'TOTP secret generated successfully.');
        } catch (Exception $e) {
            return $this->error('Failed to initialize TOTP setup.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Enable TOTP with 6-digit confirmation code.
     */
    public function totpEnable(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'secret_key' => 'required|string',
                'code'       => 'required|string|size:6',
            ]);

            if ($validator->fails()) {
                return $this->error($validator->errors()->first(), $validator->errors(), 422);
            }

            $user = $request->user();
            $result = $this->totp->enableTotp($user, $request->input('secret_key'), $request->input('code'));

            if (!$result['success']) {
                return $this->error($result['message'], [], 400);
            }

            return $this->success($result, 'Two-factor authentication enabled successfully.');
        } catch (Exception $e) {
            return $this->error('Failed to enable TOTP.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Disable TOTP authentication (requires current account password).
     */
    public function totpDisable(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->error($validator->errors()->first(), $validator->errors(), 422);
            }

            $user = $request->user();

            if (!Hash::check($request->input('password'), $user->password)) {
                return $this->error('Incorrect password. Two-Factor Authentication was not disabled.', [], 422);
            }

            $this->totp->disableTotp($user);

            return $this->success(null, 'Two-Factor Authentication disabled successfully.');
        } catch (Exception $e) {
            return $this->error('Failed to disable TOTP.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update user's TOTP login preference (requires 6-digit TOTP code).
     */
    public function updateTotpPreference(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'always_require_on_login' => 'required|boolean',
                'code'                    => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->error($validator->errors()->first(), $validator->errors(), 422);
            }

            $user = $request->user();

            if (!$this->totp->verifyUserTotpOrRecovery($user, (string) $request->input('code'))) {
                return $this->error('Invalid Authenticator code. Preference was not updated.', [], 422);
            }

            $alwaysRequire = $request->boolean('always_require_on_login');
            $this->totp->updateLoginPreference($user, $alwaysRequire);

            return $this->success([
                'always_require_on_login' => $alwaysRequire,
            ], 'Login security preference updated successfully.');
        } catch (Exception $e) {
            return $this->error('Failed to update preference.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Regenerate new backup recovery codes via API.
     */
    public function regenerateRecoveryCodes(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$this->totp->hasTotpEnabled($user)) {
                return $this->error('Two-Factor Authentication is not enabled on this account.', [], 400);
            }

            $newCodes = $this->totp->regenerateRecoveryCodes($user);

            return $this->success([
                'recovery_codes' => $newCodes,
            ], 'New emergency backup recovery codes generated successfully.');
        } catch (Exception $e) {
            return $this->error('Failed to regenerate recovery codes.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * List registered devices.
     */
    public function listDevices(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $cookieName = config('adaptive_auth.cookie_name', 'adaptive_device_token');
            $currentUuid = (string) $request->cookie($cookieName);

            $devices = $user->devices()
                ->orderByDesc('last_active_at')
                ->get()
                ->map(function ($d) use ($currentUuid) {
                    return [
                        'id'             => $d->id,
                        'device_name'    => $d->device_name,
                        'platform'       => $d->platform,
                        'browser'        => $d->browser,
                        'device_type'    => $d->device_type,
                        'ip'             => $d->last_ip,
                        'location'       => trim(($d->city ?? '') . ', ' . ($d->country ?? ''), ', '),
                        'is_current'     => $d->device_uuid === $currentUuid,
                        'is_trusted'     => (bool) $d->is_trusted,
                        'trusted_at'     => $d->trusted_at?->toIso8601String(),
                        'last_active_at' => $d->last_active_at?->toIso8601String(),
                    ];
                });

            return $this->success([
                'devices'                 => $devices,
                'has_totp'                => $this->totp->hasTotpEnabled($user),
                'always_require_on_login' => $this->totp->alwaysRequiresTotpOnLogin($user),
                'recovery_codes_count'    => $this->totp->getRemainingRecoveryCodesCount($user),
            ], 'Registered devices retrieved successfully.');
        } catch (Exception $e) {
            return $this->error('Failed to retrieve devices.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Revoke access for a specific device.
     */
    public function revokeDevice(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            $revoked = $user->revokeDevice($id);

            if (!$revoked) {
                return $this->error('Device not found or already revoked.', [], 404);
            }

            return $this->success(null, 'Device access revoked successfully.');
        } catch (Exception $e) {
            return $this->error('Failed to revoke device.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Revoke access for all devices except current one.
     */
    public function revokeOtherDevices(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $cookieName = config('adaptive_auth.cookie_name', 'adaptive_device_token');
            $currentUuid = (string) $request->cookie($cookieName);

            $revokedCount = $user->revokeOtherDevices($currentUuid);

            return $this->success([
                'revoked_count' => $revokedCount,
            ], 'All other devices have been revoked.');
        } catch (Exception $e) {
            return $this->error('Failed to revoke other devices.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Clear all sign-in audit history logs.
     */
    public function clearAuditLogs(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $count = $user->clearLoginLogs();

            return $this->success(['deleted_count' => $count], 'Audit logs cleared.');
        } catch (Exception $e) {
            return $this->error('Failed to clear audit logs.', ['error' => $e->getMessage()], 500);
        }
    }
}
