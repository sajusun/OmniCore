<?php

namespace App\Http\Controllers\Api\Auth;

use Exception;
use App\Models\User;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public array $select;
    public function __construct()
    {
        parent::__construct();
        $this->select = ['id', 'name', 'email', 'avatar', 'otp_verified_at', 'last_activity_at'];
    }

    public function Login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email'    => 'required|email|exists:users,email',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return Helper::jsonResponse(false, 'Validation failed', 422, $validator->errors());
            }

            $user = User::where('email', $request->email);

            if (!$user) {
                return Helper::jsonResponse(false, 'User not found', 404);
            }

            $user = $user->where('status', 'active')->first();

            if (!$user) {
                return Helper::jsonResponse(false, 'user is not active', 404);
            }

            if (!Hash::check($request->password, $user->password)) {
                return Helper::jsonResponse(false, 'Invalid password', 401);
            }

            //? Check if the email is verified before login is successful
            if (!$user->otp_verified_at) {
                return Helper::jsonResponse(false, 'Email not verified. Please verify your email before logging in.', 403, ['is_otp_verified' => $user->isOtpVerified]);
            } else {
                $user->update([
                    'otp'            => null,
                    'otp_expires_at' => null,
                ]);
            }

            $user->update([
                'last_activity_at' => now(),
            ]);

            //* Generate token if email is verified
            $token = auth('api')->login($user);
            if (!$user->nutritionGoal) {
                try {
                    $user->sendNotification(title: 'Nutritions Goals', body: 'Please Add Your Nutritions Goals First.', type: 'Nutritions');
                } catch (\Throwable $e) {
                    Log::error($e->getMessage());
                }
            }


            $data = User::select($this->select)->with('roles')->find(auth('api')->user()->id);
            $user->logActivity('User Login', 'User Login Detect ' . $user->email);

            return response()->json([
                'status'     => true,
                'message'    => 'Login successful',
                'code'       => 200,
                'token_type' => 'bearer',
                'token'      => $token,
                'expires_in' => auth('api')->factory()->getTTL() * 60,
                'nutritions_goal' => (bool) $user->nutritionGoal ? true : false,
                'is_subscribed' => (bool) $user->is_subscribed,
                'package_id'    => $user->activeSubscription->product_id ?? null,
                'data'          => $data,
            ], 200);
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'An error occurred during login.', 500, ['error' => $e->getMessage()]);
        }
    }

    public function refreshToken()
    {
        $refreshToken = auth('api')->refresh();

        if (empty($refreshToken)) {
            return Helper::jsonErrorResponse('Failed to refresh the token.', 401);
        }

        return response()->json([
            'status'     => true,
            'message'    => 'Access token refreshed successfully.',
            'code'       => 200,
            'token_type' => 'bearer',
            'token'      => $refreshToken,
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'data' => auth('api')->user()
        ]);
    }
}
