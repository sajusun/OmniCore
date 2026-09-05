<?php

declare(strict_types=1);

namespace App\Modules\Auth\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Auth\Models\Verification;
use App\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class RegisterApiController extends Controller
{
    private array $select = ['id', 'name', 'email', 'avatar', 'last_activity_at'];

    public function __construct(
        private readonly UserService $userService
    ) {
        parent::__construct();
    }

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'string', 'email', 'max:150', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'agree'    => ['required', 'in:true,1'],
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name'             => $request->input('name'),
                'slug'             => $this->makeSlug($request->input('name')),
                'email'            => strtolower($request->input('email')),
                'password'         => Hash::make($request->input('password')),
                'status'           => 'active',
                'last_activity_at' => now(),
            ]);

            // Automatically assign 'user' role for API registration
            $user->assignRole('user');

            // Send OTP directly via Model's HasVerification trait!
            $verification = $user->sendVerification(
                purpose: Verification::PURPOSE_EMAIL_VERIFICATION
            );

            DB::commit();

            return $this->success(
                data: [
                    'user' => User::select($this->select)->find($user->id),
                    'otp'  => $verification->code,
                ],
                message: 'Registration successful. Please check your email for the OTP.',
                status: 201
            );
        } catch (RuntimeException $e) {
            Log::error('User registration failed: ' . $e->getMessage(), ['exception' => $e]);
            DB::rollBack();
            return $this->error($e->getMessage(), null, 422);
        } catch (Exception $e) {
            Log::error('User registration failed: ' . $e->getMessage(), ['exception' => $e]);
            DB::rollBack();
            return $this->error($e->getMessage(), null, 500);
        }
    }

    public function verifyEmail(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'otp'   => ['required', 'string'],
        ]);

        try {
            $user = $this->userService->findByEmail($request->input('email'));
            if (!$user) {
                return $this->error('Invalid Email Address', null, 404);
            }

            if ($user->isEmailVerified()) {
                return $this->error('Email is already verified.', null, 409);
            }

            // Verify OTP directly via Model trait!
            $verified = $user->verifyOtp(
                code: (string) $request->input('otp'),
                purpose: Verification::PURPOSE_EMAIL_VERIFICATION
            );

            if (!$verified) {
                return $this->error('Invalid OTP code. Please try again.', null, 422);
            }

            return $this->success(
                data: [
                    'token_type'       => 'bearer',
                    'token'            => auth('api')->login($user),
                    'id'               => $user->id,
                    'name'             => $user->name,
                    'email'            => $user->email,
                    'avatar'           => $user->avatar ? url($user->avatar) : null,
                    'last_activity_at' => $user->last_activity_at,
                ],
                message: 'Email verified successfully.'
            );
        } catch (RuntimeException $e) {
            Log::error('Email verification failed: ' . $e->getMessage(), ['exception' => $e]);
            return $this->error($e->getMessage(), null, 422);
        } catch (Exception $e) {
            Log::error('Email verification failed: ' . $e->getMessage(), ['exception' => $e]);
            return $this->error($e->getMessage(), null, 500);
        }
    }

    public function resendOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        try {
            $user = $this->userService->findByEmail($request->input('email'));
            if (!$user) {
                return $this->error('Invalid Email Address', null, 404);
            }

            if ($user->isEmailVerified()) {
                return $this->error('Email is already verified.', null, 409);
            }

            // Resend directly via Model trait!
            $verification = $user->resendVerification(
                purpose: Verification::PURPOSE_EMAIL_VERIFICATION
            );

            return $this->success(
                data: [
                    'expires_at'    => $verification->expires_at?->toDateTimeString(),
                    'request_count' => $verification->request_count,
                    'otp'           => $verification->code,
                ],
                message: 'A new OTP has been sent to your email.'
            );
        } catch (RuntimeException $e) {
            Log::error('Resend OTP failed: ' . $e->getMessage(), ['exception' => $e]);
            return $this->error($e->getMessage(), null, 422);
        } catch (Exception $e) {
            Log::error('Resend OTP failed: ' . $e->getMessage(), ['exception' => $e]);
            return $this->error($e->getMessage(), null, 500);
        }
    }
}
