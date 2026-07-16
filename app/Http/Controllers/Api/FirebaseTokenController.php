<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use App\Helpers\Helper;
use App\Models\FcmToken;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\PushService;
use App\Models\FirebaseTokens;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Services\SystemMessageService;
use Illuminate\Support\Facades\Validator;

class FirebaseTokenController extends Controller
{

    public function test()
    {
        $user = User::find(auth('api')->user()->id);
        if ($user && $user->firebaseTokens) {
            $notifyData = ['title' => "notification test", 'body' => "notification test body", 'icon' => config('settings.logo')];
            foreach ($user->firebaseTokens as $firebaseToken) {
                Helper::sendNotifyMobile($firebaseToken->token, $notifyData);
            }
        }

        return response()->json([
            'status'  => true,
            'message' => 'Token saved successfully',
            'data'    => $user->firebaseTokens,
            'code'    => 200,
        ], 200);
    }


    public function generateToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token'       => 'required|string',
            'device_id'   => 'required|string',
            'platform'    => 'nullable|string',
            'device_name' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 400);
        }

        $device = FirebaseTokens::query()
            ->where('device_id', $request->device_id)->where('jwt_token', $request->token)->where('status', 'active')->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'code'    => 'DEVICE_UNAUTHORIZED',
                'message' => 'This device is not authorized.'
            ], 403);
        }

        $token = JWTAuth::fromUser($device->user);

        $device->jwt_token = $token;
        $device->updated_at = now();
        $device->save();


        return response()->json([
            'success' => true,
            'old_token' => $request->token,
            'new_token'   => $token,
        ]);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token'     => 'required|string',
            'device_id' => 'required|string',
            'platform'  => 'nullable|string',
            'device_name' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        try {
            $data = FirebaseTokens::updateOrCreate(
                [
                    'user_id'   => auth('api')->id(),
                    'device_id' => $request->device_id,
                ],
                [
                    'token'            => $request->token,
                    'status'           => 'active',
                    'jwt_token'        => request()->bearerToken(),
                    'platform'         => $request->platform ?? null,
                    'device_name'      => $request->device_name ?? null,
                    'ip_address'       => request()->ip(),
                    'user_agent'       => request()->userAgent(),
                    'last_activity_at' => now(),
                ]
            );

            return response()->json(['status'  => true, 'message' => 'Token saved successfully', 'data' => $data, 'code' => 200,], 200);
        } catch (Exception $e) {
            Log::info('Firebase Token Store Error: ' . $e->getMessage());
            return response()->json(['status'  => false, 'message' => 'No records found', 'code'    => 418, 'data'    => [],], 418);
        }
    }
    public function devices()
    {
        $devices = FirebaseTokens::where('user_id', auth('api')->id())->where('status', 'active')->latest('last_activity_at')
            ->get(['id', 'device_name', 'platform', 'ip_address', 'last_activity_at', 'created_at']);

        return response()->json([
            'status' => true,
            'data' => $devices,
        ]);
    }

    public function logoutDevice($id)
    {
        $device = FirebaseTokens::where('user_id', auth('api')->id())->findOrFail($id);
        $device->update([
            'status' => 'inactive',
            'jwt_token' => null,
        ]);
        SystemMessageService::send(Auth('api')->user(), 'A device session has been revoked from your account.');

        return response()->json([
            'status' => true,
            'message' => 'Device logged out successfully',
        ]);
    }

    /**
     * Get Single Record
     * @param $token, $device_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }
        $user_id   = auth('api')->user()->id;
        $device_id = $request->device_id;
        $data      = FirebaseTokens::where('user_id', $user_id)->where('device_id', $device_id)->first();
        if (! $data) {
            return response()->json([
                'status'  => false,
                'message' => 'No records found',
                'code'    => 404,
                'data'    => [],
            ], 404);
        }
        return response()->json([
            'status'  => true,
            'message' => 'Token fetched successfully',
            'data'    => $data,
            'code'    => 200,
        ], 200);
    }

    /**
     * Delete Token Single Record
     * @param $token, $device_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        $user = FirebaseTokens::where('user_id', auth('api')->user()->id)->where('device_id', $request->device_id);
        if ($user) {
            $user->delete();
            return response()->json([
                'status'  => true,
                'message' => 'Token deleted successfully',
                'code'    => 200,
            ], 200);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'No records found',
                'code'    => 404,
            ], 404);
        }
    }

    // test token store
    public function test_token_store(Request $request)
    {
        $request->validate([
            'user_id'   => 'required|string',
            'fcm_token' => 'required|string',
        ]);

        FcmToken::updateOrCreate(
            ['user_id' => $request->user_id],
            ['fcm_token' => $request->fcm_token]
        );

        return response()->json(['message' => 'Token saved']);
    }

    public function sendCall(Request $request, PushService $push)
    {
        $request->validate([
            'receiver_id' => 'required',
            'caller_id'   => 'required',
            'caller_name' => 'required',
            'call_type'   => 'required|in:video,voice',
        ]);

        $tokenRow = FcmToken::where('user_id', $request->receiver_id)->first();
        if (! $tokenRow) {
            return response()->json(['message' => 'Receiver FCM token not found'], 404);
        }


        $callId     = (string) Str::uuid();

        $data = [
            'zego'        => 'true',
            'call_id'     => $callId,
            'caller_id'   => $request->caller_id,
            'caller_name' => $request->caller_name,
            'call_type'   => $request->call_type,
            'resource_id' => 'lorenzobook',
        ];

        try {
            $resp = $push->toToken(
                $tokenRow->fcm_token,
                $data,
                'Incoming Call',
                "{$request->caller_name} is calling"
            );

            return response()->json([
                'status'      => true,
                'call_id'     => $callId,

                'result'      => $resp,
            ]);
        } catch (\Throwable $e) {
            if (str_contains($e->getMessage(), 'UNREGISTERED')) {
                $tokenRow->delete();
            }
            return response()->json([
                'status' => false,
                'error'  => $e->getMessage(),
            ], 500);
        }
    }
}
