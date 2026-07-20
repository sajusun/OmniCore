<?php

namespace App\Http\Controllers\Api\Auth;

use App\Services\UserService;
use Exception;
use App\Models\User;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public array $select;
    public function __construct(private UserService $userService)
    {
        parent::__construct();
        $this->select = ['id', 'name', 'email', 'avatar', 'last_activity_at'];
    }

    public function Login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email'    => 'required|email|exists:users,email',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return $this->error($validator->errors()->first(), $validator->errors(), 422);
            }

            $user = $this->userService->findByEmail($request->email);


            if ($user->status !== 'active') {
                return $this->error('User is not active', [], 404);
            }

            if (!Hash::check($request->password, $user->password)) {
                return $this->error('Invalid password', [], 401);
            }

            //? Check if the email is verified before login is successful
            if (!$user->isEmailVerified()) {
                return $this->error('Email not verified. Please verify your email before logging in.', [], 403);
            }

            $user->update([
                'last_activity_at' => now(),
            ]);

            $data = $user->only($this->select);

            return response()->json([
                'status'     => true,
                'message'    => 'Login successful',
                'code'       => 200,
                'token_type' => 'bearer',
                'token'      => auth('api')->login($user),
                'expires_in' => auth('api')->factory()->getTTL() * 60,
                'data'       => $data,
            ], 200);
        } catch (Exception $e) {
            return $this->error('An error occurred during login.', ['error' => $e->getMessage()], 500);
        }
    }

    public function refreshToken()
    {
        $refreshToken = auth('api')->refresh();

        if (empty($refreshToken)) {
            return $this->error('Failed to refresh the token.', [], 401);
        }

        return response()->json([
            'status'     => true,
            'message'    => 'Access token refreshed successfully.',
            'code'       => 200,
            'token_type' => 'bearer',
            'token'      => $refreshToken,
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'data'       => auth('api')->user()
        ]);
    }
}
