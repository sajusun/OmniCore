<?php

namespace App\Modules\Notification\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Notification\Services\FirebaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FirebaseTokenApiController extends Controller
{
    public function __construct(protected FirebaseService $firebaseService)
    {
        parent::__construct();
    }

    /**
     * Get all active device sessions for authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth('api')->user();
        if (! $user) {
            return $this->error('User not authenticated.', null, 401);
        }

        $jwtToken = $request->bearerToken();
        $devices = $this->firebaseService->getDevices($user, $jwtToken);

        return $this->success($devices, 'Active devices retrieved successfully.');
    }

    /**
     * Save / Update Firebase Token & Register Device Session
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token'       => 'required|string',
            'device_id'   => 'required|string',
            'device_name' => 'nullable|string|max:255',
            'platform'    => 'nullable|string|max:50',
        ]);

        $user = auth('api')->user();
        $firebaseToken = $this->firebaseService->registerDevice($user, $data);

        return $this->success($firebaseToken, 'Firebase token and device session saved successfully.');
    }

    /**
     * Revoke / Remove a Specific Device Session
     */
    public function destroy(Request $request, ?string $deviceId = null): JsonResponse
    {
        $targetDeviceId = $deviceId ?? $request->input('device_id');
        $id = $request->input('id');

        $user = auth('api')->user();

        if ($user) {
            if ($id) {
                $this->firebaseService->deleteById($user, (int) $id);
            } elseif ($targetDeviceId) {
                $this->firebaseService->deleteByDeviceId($user, $targetDeviceId);
            } else {
                return $this->error('Please provide a device_id or id to revoke.', null, 422);
            }
        } elseif ($targetDeviceId) {
            $this->firebaseService->deactivateDevice($targetDeviceId);
        } else {
            return $this->error('Device ID is required.', null, 422);
        }

        return $this->success(null, 'Device session revoked successfully.');
    }

    /**
     * Revoke All Other Devices (except the current one)
     */
    public function revokeOthers(Request $request): JsonResponse
    {
        $user = auth('api')->user();
        if (! $user) {
            return $this->error('User not authenticated.', null, 401);
        }

        $currentJwt = $request->bearerToken();
        if (empty($currentJwt)) {
            return $this->error('Bearer token is required to identify the current session.', null, 400);
        }

        $revokedCount = $this->firebaseService->logoutOtherDevices($user, $currentJwt);

        return $this->success(['revoked_count' => $revokedCount], 'All other device sessions have been revoked successfully.');
    }

    /**
     * Revoke All Devices for current user
     */
    public function revokeAll(Request $request): JsonResponse
    {
        $user = auth('api')->user();
        if (! $user) {
            return $this->error('User not authenticated.', null, 401);
        }

        $revokedCount = $this->firebaseService->logoutAllDevices($user);

        return $this->success(['revoked_count' => $revokedCount], 'All device sessions have been revoked successfully.');
    }

    /**
     * Refresh Last Activity & Device Info
     */
    public function touch(Request $request): JsonResponse
    {
        $request->validate([
            'device_id' => 'required|string',
        ]);

        $jwtToken = $request->bearerToken();
        if ($jwtToken) {
            $this->firebaseService->updateLastActivity($jwtToken);
        }

        $this->firebaseService->updateDeviceInformation($request->device_id, $request->all());

        return $this->success(null, 'Activity updated successfully.');
    }
}
