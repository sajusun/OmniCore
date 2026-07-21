<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use Exception;
use App\Models\User;
use RuntimeException;
use App\Helpers\Helper;
use App\Models\Verification;
use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Services\VerificationService;

class RegisterController extends Controller
{
    private array $select = ['id', 'name', 'email', 'avatar', 'last_activity_at'];

    public function __construct(private readonly VerificationService $verificationService, private UserService $userService)
    {
        parent::__construct();
    }

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name'      => ['required', 'string', 'max:100'],
            'email'     => ['required', 'string', 'email', 'max:150', 'unique:users'],
            'password'  => ['required', 'string', 'min:6', 'confirmed'],
            'agree'     => ['required', 'in:true,1'],
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

            // Send OTP via the Verification Module (stores in verifications table)
            $verifcation = $this->verificationService->send(user: $user, purpose: Verification::PURPOSE_EMAIL_VERIFICATION);

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Registration successful. Please check your email for the OTP.',
                'code'    => 200,
                'otp'      => $verifcation->code,
                'data'    => User::select($this->select)->find($user->id),
            ], 200);
        } catch (RuntimeException $e) {
            Log::error('User registration failed: ' . $e->getMessage(), ['exception' => $e]);
            DB::rollBack();
            return $this->error(message: $e->getMessage(), status: 422);
        } catch (Exception $e) {
            Log::error('User registration failed: ' . $e->getMessage(), ['exception' => $e]);
            DB::rollBack();
            return $this->error(message: $e->getMessage(), status: 500);
        }
    }


    public function VerifyEmail(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'otp'   => ['required', 'string'],
        ]);

        try {
            $user = $this->userService->findByEmail($request->input('email'));
            if (!$user) {
                return $this->error(message: 'Invalid Email Address', status: 404);
            }

            if ($user->isEmailVerified()) {
                return $this->error(message: 'Email is already verified.', status: 409);
            }

            $verified = $this->verificationService->verifyOtp(user: $user, purpose: Verification::PURPOSE_EMAIL_VERIFICATION, code: (string) $request->input('otp'));

            if (!$verified) {
                return $this->error(message: 'Invalid OTP code. Please try again.', status: 422);
            }

            return $this->success(message: 'Email verified successfully.', status: 200);
        } catch (RuntimeException $e) {
            Log::error('Email verification failed: ' . $e->getMessage(), ['exception' => $e]);
            return $this->error(message: $e->getMessage(), status: 422);
        } catch (Exception $e) {
            log::error('Email verification failed: ' . $e->getMessage(), ['exception' => $e]);
            return $this->error(message: $e->getMessage(), status: 500);
        }
    }

    public function ResendOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        try {
            $user = $this->userService->findByEmail($request->input('email'));
            if (!$user) {
                return $this->error(message: 'Invalid Email Address', status: 404);
            }

            if ($user->isEmailVerified()) {
                return $this->error(message: 'Email is already verified.', status: 409);
            }

            $verification = $this->verificationService->resend(user: $user, purpose: Verification::PURPOSE_EMAIL_VERIFICATION, type: 'otp');

            return $this->success(
                message: 'A new OTP has been sent to your email.',
                status: 200,
                data: [
                    'expires_at' => $verification->expires_at?->toDateTimeString(),
                    'request_count' => $verification->request_count,
                    'otp' => $verification->code,
                ]
            );
        } catch (RuntimeException $e) {
            log::error('Resend OTP failed: ' . $e->getMessage(), ['exception' => $e]);
            return $this->error(message: $e->getMessage(), status: 422);
        } catch (Exception $e) {
            log::error('Resend OTP failed: ' . $e->getMessage(), ['exception' => $e]);
            return $this->error(message: $e->getMessage(), status: 500);
        }
    }
}
