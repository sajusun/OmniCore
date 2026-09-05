<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FirebaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FirebaseTokenController extends Controller
{
    public function __construct(protected FirebaseService $firebaseService)
    {
        parent::__construct();
    }

    /**
     * Save / Update Firebase Token
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

        return $this->success($firebaseToken, 'Firebase token saved successfully.');
    }

    /**
     * Remove Token
     */
    public function destroy(Request $request): JsonResponse
    {
        $request->validate([
            'device_id' => 'required|string',
        ]);

        $user = auth('api')->user();
        if ($user) {
            $this->firebaseService->deleteByDeviceId($user, $request->device_id);
        } else {
            $this->firebaseService->deactivateDevice($request->device_id);
        }

        return $this->success(null, 'Firebase token removed successfully.');
    }

    /**
     * Refresh Last Activity
     */
    public function touch(Request $request): JsonResponse
    {
        $request->validate([
            'device_id' => 'required|string',
        ]);

        $jwtToken = request()->bearerToken();
        if ($jwtToken) {
            $this->firebaseService->updateLastActivity($jwtToken);
        }

        $this->firebaseService->updateDeviceInformation($request->device_id, $request->all());

        return $this->success(null, 'Activity updated.');
    }
}
