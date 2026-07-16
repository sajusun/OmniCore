<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Models\FirebaseToken;
use App\Http\Controllers\Controller;

class FirebaseTokenController extends Controller
{
    /**
     * Save / Update Firebase Token
     */
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'device_id' => 'required|string',
            'device_name' => 'nullable|string|max:255',
            'platform' => 'nullable|string|max:50',
        ]);

        $firebaseToken = FirebaseToken::updateOrCreate(
            [
                'device_id' => $request->device_id,
            ],
            [
                'user_id'          => auth('api')->id(),
                'token'            => $request->token,
                'device_name'      => $request->device_name,
                'platform'         => $request->platform,
                'jwt_token'        => request()->bearerToken(),
                'ip_address'       => $request->ip(),
                'user_agent'       => $request->userAgent(),
                'last_activity_at' => now(),
                'status'           => 'active',
            ]
        );

        return Helper::jsonResponse( true,'Firebase token saved successfully.', 200,$firebaseToken);
    }

    /**
     * Remove Token
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'device_id' => 'required|string',
        ]);
        FirebaseToken::where('device_id', $request->device_id)->where('user_id', auth('api')->id())->delete();
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

        FirebaseToken::where('device_id', $request->device_id)
            ->update([
                'last_activity_at' => now(),
                'jwt_token' => request()->bearerToken(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

        return Helper::jsonResponse(true,'Activity updated.',200);
    }
}
