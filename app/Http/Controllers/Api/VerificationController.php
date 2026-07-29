<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Models\User;
use RuntimeException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\VerificationService;
use Illuminate\Http\RedirectResponse;

class VerificationController extends Controller
{
    public function __construct(private readonly VerificationService $verificationService) {}


    public function send(Request $request): JsonResponse
    {
        $data = $request->validate([
            'purpose' => ['required', 'string', 'in:email_verification,password_reset'],
            'type'    => ['nullable', 'string', 'in:otp,token'],
        ]);

        /** @var User $user */
        $user = Auth::user();

        try {
            $verification = $this->verificationService->send(
                user:    $user,
                purpose: $data['purpose'],
                type:    $data['type'] ?? null,
            );
        } catch (RuntimeException $e) {
            return $this->error($e->getMessage());
        }

        return $this->success(
            message: 'Verification code sent successfully.',
            data: [
                'verification_type' => $verification->verification_type,
                'purpose'           => $verification->purpose,
                'expires_at'        => $verification->expires_at?->toDateTimeString(),
            ],
        );
    }


    public function verifyOtp(Request $request): JsonResponse
    {
        $data = $request->validate([
            'purpose' => ['required', 'string', 'in:email_verification,password_reset'],
            'code'    => ['required', 'string'],
        ]);

        /** @var User $user */
        $user = Auth::user();

        try {
            $verified = $this->verificationService->verifyOtp(
                user:    $user,
                purpose: $data['purpose'],
                code:    $data['code'],
            );
        } catch (RuntimeException $e) {
            return $this->error($e->getMessage());
        }

        if (! $verified) {
            return $this->error('The OTP you entered is incorrect. Please try again.', 422);
        }

        return $this->success('OTP verified successfully.');
    }


    public function verifyToken(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'token'   => ['required', 'string'],
            'purpose' => ['required', 'string', 'in:email_verification,password_reset'],
        ]);

        $redirectUrl = $this->verificationService->verifyToken(
            purpose:    $data['purpose'],
            plainToken: $data['token'],
        );

        return redirect()->to($redirectUrl);
    }


    public function resend(Request $request): JsonResponse
    {
        $data = $request->validate([
            'purpose' => ['required', 'string', 'in:email_verification,password_reset'],
            'type'    => ['nullable', 'string', 'in:otp,token'],
        ]);

        /** @var User $user */
        $user = Auth::user();

        try {
            $verification = $this->verificationService->resend(
                user:    $user,
                purpose: $data['purpose'],
                type:    $data['type'] ?? null,
            );
        } catch (RuntimeException $e) {
            return $this->error($e->getMessage());
        }

        return $this->success(
            message: 'Verification code resent successfully.',
            data: [
                'verification_type' => $verification->verification_type,
                'purpose'           => $verification->purpose,
                'expires_at'        => $verification->expires_at?->toDateTimeString(),
                'request_count'     => $verification->request_count,
            ],
        );
    }


    protected function success($data = null, $message = 'Success', $status = 200): JsonResponse
    {
        if (is_string($data) && $message === 'Success') {
            $message = $data;
            $data = null;
        }

        $payload = ['success' => true, 'message' => $message];

        if ($data !== null) {
            $payload['data'] = $data;
        }

        return response()->json($payload, $status);
    }


    protected function error($message = 'Something went wrong', $errors = null, $status = 422): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], $status);
    }
}
