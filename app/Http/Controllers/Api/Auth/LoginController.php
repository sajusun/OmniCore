<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

    public function Login(Request $request): JsonResponse
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

            // Check if the email is verified before login is successful
            if (!$user->isEmailVerified()) {
                return $this->error('Email not verified. Please verify your email before logging in.', [], 403);
            }

            $user->update([
                'last_activity_at' => now(),
            ]);

            $userData = $user->only($this->select);

            return $this->success([
                'token_type' => 'bearer',
                'token'      => auth('api')->login($user),
                'expires_in' => auth('api')->factory()->getTTL() * 60,
                'user'       => $userData,
                'data'       => $userData,
            ], 'Login successful');
        } catch (Exception $e) {
            return $this->error('An error occurred during login.', ['error' => $e->getMessage()], 500);
        }
    }

    public function refreshToken(): JsonResponse
    {
        $refreshToken = auth('api')->refresh();

        if (empty($refreshToken)) {
            return $this->error('Failed to refresh the token.', [], 401);
        }

        return $this->success([
            'token_type' => 'bearer',
            'token'      => $refreshToken,
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user'       => auth('api')->user(),
        ], 'Access token refreshed successfully.');
    }
}
