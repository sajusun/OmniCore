<?php

declare(strict_types=1);

namespace App\Modules\Auth\Traits;

use App\Modules\Auth\Models\Verification;
use App\Modules\Auth\Services\VerificationService;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasVerification
{
    /**
     * Get all verifications for this model polymorphically.
     */
    public function verifications(): MorphMany
    {
        return $this->morphMany(Verification::class, 'verifiable');
    }

    /**
     * Send a new verification code/token to this model.
     *
     * @param string $purpose e.g. 'email_verification' | 'password_reset'
     * @param string|null $type 'otp' | 'token'
     * @param string|null $channel 'email' | 'sms'
     * @return Verification
     */
    public function sendVerification(
        string $purpose = Verification::PURPOSE_EMAIL_VERIFICATION,
        ?string $type = null,
        ?string $channel = null
    ): Verification {
        return app(VerificationService::class)->send($this, $purpose, $type, $channel);
    }

    /**
     * Verify an OTP submitted for this model.
     *
     * @param string $code
     * @param string $purpose
     * @return bool
     */
    public function verifyOtp(
        string $code,
        string $purpose = Verification::PURPOSE_EMAIL_VERIFICATION
    ): bool {
        return app(VerificationService::class)->verifyOtp($this, $purpose, $code);
    }

    /**
     * Resend verification code/token with cooldown & rate-limit checks.
     *
     * @param string $purpose
     * @param string|null $type
     * @param string|null $channel
     * @return Verification
     */
    public function resendVerification(
        string $purpose = Verification::PURPOSE_EMAIL_VERIFICATION,
        ?string $type = null,
        ?string $channel = null
    ): Verification {
        return app(VerificationService::class)->resend($this, $purpose, $type, $channel);
    }

    /**
     * Check if this model is verified for a given purpose.
     */
    public function isVerified(string $purpose = Verification::PURPOSE_EMAIL_VERIFICATION): bool
    {
        return app(VerificationService::class)->isVerified($this, $purpose);
    }

    /**
     * Convenience method for email verification check.
     */
    public function isEmailVerified(): bool
    {
        return $this->isVerified(Verification::PURPOSE_EMAIL_VERIFICATION);
    }
}
