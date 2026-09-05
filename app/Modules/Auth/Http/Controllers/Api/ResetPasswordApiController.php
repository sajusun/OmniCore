<?php

declare(strict_types=1);

namespace App\Modules\Auth\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Models\Verification;
use App\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResetPasswordApiController extends Controller
{
    public array $select;

    public function __construct(private readonly UserService $userService)
    {
        parent::__construct();
        $this->select = ['id', 'name', 'email', 'avatar'];
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        try {
            $user = $this->userService->findByEmail($request->input('email'));

            if (!$user) {
                return $this->error(message: 'Invalid Email Address', status: 404);
            }

            // Send OTP directly via Model trait!
            $verification = $user->sendVerification(
                purpose: Verification::PURPOSE_PASSWORD_RESET
            );

            return $this->success(
                data: ['otp' => $verification->code],
                message: 'Code sent successfully. Please check your email.',
                status: 200
            );
        } catch (Exception $e) {
            return $this->error(message: $e->getMessage(), status: 500);
        }
    }

    public function resetSecretKey(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp'   => 'required|string',
        ]);

        try {
            $user = $this->userService->findByEmail($request->email);

            if (!$user) {
                return $this->error(message: 'User not found', status: 404);
            }

            // Verify OTP directly via Model trait!
            $verified = $user->verifyOtp(
                code: (string) $request->input('otp'),
                purpose: Verification::PURPOSE_PASSWORD_RESET
            );

            if (!$verified) {
                return $this->error(message: 'Invalid OTP', status: 400);
            }

            $token = $this->userService->createPasswordResetToken($user);

            return $this->success(
                data: ['secret_key' => $token],
                message: 'OTP verified successfully.',
                status: 200
            );
        } catch (Exception $e) {
            return $this->error(message: $e->getMessage(), status: 500);
        }
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email'      => 'required|email|exists:users,email',
            'secret_key' => 'required|string',
            'password'   => 'required|string|min:6|confirmed',
        ]);

        try {
            $user = $this->userService->findByEmail($request->email);

            if (!$user) {
                return $this->error(message: 'User not found', status: 404);
            }

            $tokenData = $this->userService->getResetTokenUser($request->secret_key, $request->email);
            if (!$tokenData) {
                return $this->error(message: 'Invalid Secret Key', status: 419);
            }

            if (!$this->userService->isTokenValid($tokenData)) {
                return $this->error(message: 'Secret Key expired', status: 419);
            }

            $this->userService->updatePassword($user, $request->password);
            $this->userService->revokePasswordToken($user);

            return $this->success(message: 'Password reset successfully.', status: 200);
        } catch (Exception $e) {
            return $this->error(message: $e->getMessage(), status: 500);
        }
    }
}
