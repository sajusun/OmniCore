<?php

declare(strict_types=1);

namespace App\Modules\AdaptiveAuth\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\AdaptiveAuth\Models\DeviceLoginChallenge;
use App\Modules\AdaptiveAuth\Services\AdaptiveAuthService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdaptiveAuthApiController extends Controller
{
    public function __construct(
        protected AdaptiveAuthService $adaptiveAuth
    ) {
        parent::__construct();
    }

    /**
     * Complete adaptive 2FA challenge via API.
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
                } catch (\Throwable $e) {
                    // Fallback to Sanctum if JWT fails
                }
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
                return $this->error($result['message'], $result, 429);
            }

            return $this->success([], $result['message']);
        } catch (Exception $e) {
            return $this->error('Failed to resend verification code.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * List user's registered devices (Security Dashboard).
     */
    public function listDevices(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return $this->error('Unauthenticated', [], 401);
        }

        $devices = $user->devices()
            ->orderByDesc('last_active_at')
            ->get([
                'id',
                'device_name',
                'platform',
                'browser',
                'device_type',
                'last_ip',
                'city',
                'country',
                'is_trusted',
                'trusted_until',
                'last_active_at',
                'revoked_at',
            ]);

        return $this->success([
            'devices' => $devices,
        ], 'Devices retrieved successfully.');
    }

    /**
     * Revoke access for a specific device.
     */
    public function revokeDevice(Request $request, int $deviceId): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return $this->error('Unauthenticated', [], 401);
        }

        $device = $user->devices()->find($deviceId);

        if (!$device) {
            return $this->error('Device not found.', [], 404);
        }

        $device->revoke();

        return $this->success([], 'Device access revoked successfully.');
    }

    /**
     * Clear all sign-in audit logs for the authenticated user via API.
     */
    public function clearAuditLogs(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return $this->error('Unauthenticated', [], 401);
        }

        $deleted = $user->clearLoginLogs();

        return $this->success([
            'deleted_count' => $deleted,
        ], 'Audit logs cleared successfully.');
    }
}

