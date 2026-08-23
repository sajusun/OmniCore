<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Services\FirebaseService;
use App\Http\Controllers\Controller;

class FirebaseTokenController extends Controller
{
    public function __construct(protected FirebaseService $firebaseService) {}

    /**
     * Save / Update Firebase Token
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'token'       => 'required|string',
            'device_id'   => 'required|string',
            'device_name' => 'nullable|string|max:255',
            'platform'    => 'nullable|string|max:50',
        ]);

        $user = auth('api')->user();
        $firebaseToken = $this->firebaseService->registerDevice($user, $data);

        return Helper::jsonResponse(true, 'Firebase token saved successfully.', 200, $firebaseToken);
    }

    /**
     * Remove Token
     */
    public function destroy(Request $request)
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

        return Helper::jsonResponse(true, 'Firebase token removed successfully.', 200);
    }

    /**
     * Refresh Last Activity
     */
    public function touch(Request $request)
    {
        $request->validate([
            'device_id' => 'required|string',
        ]);

        $jwtToken = request()->bearerToken();
        if ($jwtToken) {
            $this->firebaseService->updateLastActivity($jwtToken);
        }

        $this->firebaseService->updateDeviceInformation($request->device_id, $request->all());

        return Helper::jsonResponse(true, 'Activity updated.', 200);
    }
}
