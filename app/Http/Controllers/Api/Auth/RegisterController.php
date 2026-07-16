<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use Exception;
use App\Models\User;
use RuntimeException;
use App\Helpers\Helper;
use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Services\VerificationService;

class RegisterController extends Controller
{
    /** @var list<string> */
    private array $select = ['id', 'name', 'email', 'avatar', 'last_activity_at'];

    public function __construct(private readonly VerificationService $verificationService,)
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

            do {
                $slug = 'user_' . random_int(1_000_000_000, 9_999_999_999);
            } while (User::where('slug', $slug)->exists());

            $user = User::create([
                'name'             => $request->input('name'),
                'slug'             => $slug,
                'email'            => strtolower($request->input('email')),
                'password'         => Hash::make($request->input('password')),
                'status'           => 'active',
                'last_activity_at' => now(),
            ]);

            // Send OTP via the Verification Module (stores in verifications table)
            $verifcation = $this->verificationService->send(user: $user, purpose: 'email_verification');

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Registration successful. Please check your email for the OTP.',
                'code'    => 200,
                'otp'      => $verifcation->code,
                'data'    => User::select($this->select)->with('roles')->find($user->id),
            ], 200);
        } catch (RuntimeException $e) {
            Log::error('User registration failed: ' . $e->getMessage(), ['exception' => $e]);
            DB::rollBack();
            return Helper::jsonErrorResponse($e->getMessage(), 422);
        } catch (Exception $e) {
            Log::error('User registration failed: ' . $e->getMessage(), ['exception' => $e]);
            DB::rollBack();
            return Helper::jsonErrorResponse('User registration failed.', 500, [$e->getMessage()]);
        }
    }


    public function VerifyEmail(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'otp'   => ['required', 'string'],
        ]);

        try {
            $user = User::where('email', $request->input('email'))->firstOrFail();

            // Guard: already verified
            if ($user->isEmailVerified()) {
                return Helper::jsonErrorResponse('Email is already verified.', 409);
            }

            $verified = $this->verificationService->verifyOtp(
                user: $user,
                purpose: Verification::PURPOSE_EMAIL_VERIFICATION,
                code: (string) $request->input('otp'),
            );

            if (!$verified) {
                return Helper::jsonErrorResponse('Invalid OTP code. Please try again.', 422);
            }

            return Helper::jsonResponse(true, 'Email verified successfully.', 200);
        } catch (RuntimeException $e) {
            // Covers: expired, blocked, max-attempts
            Log::error('Email verification failed: ' . $e->getMessage(), ['exception' => $e]);
            return Helper::jsonErrorResponse($e->getMessage(), 422);
        } catch (Exception $e) {
            log::error('Email verification failed: ' . $e->getMessage(), ['exception' => $e]);
            return Helper::jsonErrorResponse($e->getMessage(), 500);
        }
    }

    public function ResendOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        try {
            $user = User::where('email', $request->input('email'))->firstOrFail();

            if ($user->isEmailVerified()) {
                return Helper::jsonErrorResponse('Email is already verified.', 409);
            }

            $verification = $this->verificationService->resend(
                user: $user,
                purpose: Verification::PURPOSE_EMAIL_VERIFICATION,
                type: 'otp',
            );

            return Helper::jsonResponse(true, 'A new OTP has been sent to your email.', 200, [
                'expires_at'    => $verification->expires_at?->toDateTimeString(),
                'request_count' => $verification->request_count,
                'otp'           => $verification->code, // For testing purposes; remove in production
            ]);

        } catch (RuntimeException $e) {
            // Covers: cooldown, max-resend, blocked
            log::error('Resend OTP failed: ' . $e->getMessage(), ['exception' => $e]);
            return Helper::jsonErrorResponse($e->getMessage(), 422);
        } catch (Exception $e) {
            log::error('Resend OTP failed: ' . $e->getMessage(), ['exception' => $e]);
            return Helper::jsonErrorResponse($e->getMessage(), 500);
        }
    }
}
