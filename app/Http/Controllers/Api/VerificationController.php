<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\VerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class VerificationController extends Controller
{
    public function __construct(private readonly VerificationService $verificationService)
    {
        parent::__construct();
    }

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
            data: [
                'verification_type' => $verification->verification_type,
                'purpose'           => $verification->purpose,
                'expires_at'        => $verification->expires_at?->toDateTimeString(),
            ],
            message: 'Verification code sent successfully.'
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

        if (!$verified) {
            return $this->error('The OTP you entered is incorrect. Please try again.', null, 422);
        }

        return $this->success(null, 'OTP verified successfully.');
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
            data: [
                'verification_type' => $verification->verification_type,
                'purpose'           => $verification->purpose,
                'expires_at'        => $verification->expires_at?->toDateTimeString(),
                'request_count'     => $verification->request_count,
            ],
            message: 'Verification code resent successfully.'
        );
    }
}
