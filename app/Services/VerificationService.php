<?php

declare(strict_types=1);

namespace App\Services;

use App\Mail\OtpMail;
use App\Mail\VerificationLinkMail;
use App\Models\User;
use App\Models\Verification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use RuntimeException;

class VerificationService
{
    // -------------------------------------------------------------------------
    // Public API
    // -------------------------------------------------------------------------

    /**
     * Create and dispatch a new verification for the given user.
     *
     * @param  User   $user
     * @param  string $purpose  e.g. 'email_verification' | 'password_reset'
     * @param  string|null $type  'otp' | 'token' - falls back to config default
     * @return Verification
     *
     * @throws RuntimeException
     */
    public function send(User $user, string $purpose, ?string $type = null): Verification
    {
        $type = $type ?? config('verification.default_type', 'otp');

        $this->checkBlocked($user, $purpose);

        return DB::transaction(function () use ($user, $purpose, $type): Verification {
            $this->expireOld($user, $purpose);

            $verification = $this->createVerification($user, $purpose, $type);

            $this->sendMail($user, $verification, $type);

            return $verification;
        });
    }

    /**
     * Verify an OTP code submitted by the user.
     *
     * @param  User   $user
     * @param  string $purpose
     * @param  string $code
     * @return bool
     *
     * @throws RuntimeException
     */
    public function verifyOtp(User $user, string $purpose, string $code): bool
    {
        $verification = Verification::query()->where('user_id', $user->id)->where('purpose', $purpose)->where('verification_type', 'otp')->where('status', 'pending')->latest()->firstOrFail();

        $this->assertNotExpired($verification);
        $this->assertNotBlocked($verification);

        if ($verification->code !== $code) {
            $this->incrementAttempts($verification);

            return false;
        }

        $this->markVerified($verification);

        return true;
    }

    /**
     * Verify a plain-text token received via e-mail link.
     *
     * @param  string $purpose
     * @param  string $plainToken
     * @return string  Redirect URL (success or failure)
     */
    public function verifyToken(string $purpose, string $plainToken): string
    {
        $verification = Verification::query()->where('purpose', $purpose)->where('verification_type', 'token')->where('status', 'pending')->latest()->first();

        if ($verification === null || $verification->isExpired() || $verification->isBlocked() || ! Hash::check($plainToken, $verification->code)) {
            return config('verification.failed_redirect_url', '/verification/failed');
        }

        $this->markVerified($verification);

        return config('verification.success_redirect_url', '/verification/success');
    }

    /**
     * Resend a verification (respects cooldown, max requests, and block rules).
     *
     * @param  User   $user
     * @param  string $purpose
     * @param  string|null $type
     * @return Verification
     *
     * @throws RuntimeException
     */
    public function resend(User $user, string $purpose, ?string $type = null): Verification
    {
        $type = $type ?? config('verification.default_type', 'otp');

        $this->checkBlocked($user, $purpose);
        $this->checkCooldown($user, $purpose);

        return DB::transaction(function () use ($user, $purpose, $type): Verification {
            $this->expireOld($user, $purpose);

            $latest = Verification::query()->where('user_id', $user->id)->where('purpose', $purpose)->latest()->first();

            $requestCount = ($latest?->request_count ?? 0) + 1;
            $maxRequests  = (int) config('verification.max_resend_requests', 5);

            $blockedUntil = null;

            if ($requestCount >= $maxRequests) {
                $blockHours   = (int) config('verification.block_hours', 24);
                $blockedUntil = Carbon::now()->addHours($blockHours);
            }

            $newVerification = $this->createVerification(user: $user, purpose: $purpose, type: $type, requestCount: $requestCount, blockedUntil: $blockedUntil);

            if ($blockedUntil === null) {
                $this->sendMail($user, $newVerification, $type);
            }

            return $newVerification;
        });
    }

    // -------------------------------------------------------------------------
    // Private Helpers
    // -------------------------------------------------------------------------

    /**
     * Generate a numeric OTP of configured digit length.
     */
    private function generateOtp(): string
    {
        $digits = (int) config('verification.otp_digits', 6);
        $min    = (int) ('1' . str_repeat('0', $digits - 1));
        $max    = (int) str_repeat('9', $digits);

        return (string) random_int($min, $max);
    }

    /**
     * Generate a secure random token and return [plain, hashed] pair.
     *
     * @return array{plain: string, hashed: string}
     */
    private function generateToken(): array
    {
        $length = (int) config('verification.token_length', 64);
        $plain  = Str::random($length);

        return [
            'plain'  => $plain,
            'hashed' => Hash::make($plain),
        ];
    }

    /**
     * Persist a new Verification record.
     */
    private function createVerification(User $user, string $purpose,string $type,int $requestCount = 1,?Carbon $blockedUntil = null): Verification {
        $expiresAt = match ($type) {
            'otp'   => Carbon::now()->addMinutes((int) config('verification.otp_expiry_minutes', 10)),
            'token' => Carbon::now()->addMinutes((int) config('verification.token_expiry_minutes', 60)),
            default => throw new RuntimeException("Unsupported verification type: {$type}"),
        };

        if ($type === 'otp') {
            $code      = $this->generateOtp();
            $plainCode = null;
        } else {
            ['plain' => $plain, 'hashed' => $hashed] = $this->generateToken();
            $code      = $hashed;
            $plainCode = $plain;
        }

        /** @var Verification $verification */
        $verification = Verification::query()->create([
            'user_id'           => $user->id,
            'verification_type' => $type,
            'purpose'           => $purpose,
            'code'              => $code,
            'attempts'          => 0,
            'request_count'     => $requestCount,
            'last_requested_at' => Carbon::now(),
            'blocked_until'     => $blockedUntil,
            'expires_at'        => $expiresAt,
            'verified_at'       => null,
            'status'            => 'pending',
        ]);

        // Attach the plain token temporarily so sendMail() can use it.
        if ($plainCode !== null) {
            $verification->setRelation('_plainToken', (object) ['value' => $plainCode]);
        }

        return $verification;
    }

    /**
     * Expire all currently pending verifications for user + purpose.
     */
    private function expireOld(User $user, string $purpose): void
    {
        Verification::query()->where('user_id', $user->id)->where('purpose', $purpose)->where('status', 'pending')->update(['status' => 'expired']);
    }

    /**
     * Enforce cooldown between resend requests.
     *
     * @throws RuntimeException
     */
    private function checkCooldown(User $user, string $purpose): void
    {
        $cooldownSeconds = (int) config('verification.resend_cooldown_seconds', 60);

        $latest = Verification::query()->where('user_id', $user->id)->where('purpose', $purpose)->latest()->first();

        if ($latest === null) {
            return;
        }

        $nextAllowed = Carbon::parse($latest->last_requested_at)->addSeconds($cooldownSeconds);

        if (Carbon::now()->lessThan($nextAllowed)) {
            $remaining = (int) Carbon::now()->diffInSeconds($nextAllowed, false);
            throw new RuntimeException(
                "Please wait {$remaining} second(s) before requesting a new code."
            );
        }
    }

    /**
     * Check whether the user is currently blocked from requesting verifications.
     *
     * @throws RuntimeException
     */
    private function checkBlocked(User $user, string $purpose): void
    {
        $latest = Verification::query()->where('user_id', $user->id)->where('purpose', $purpose)->whereNotNull('blocked_until')->latest()->first();

        if ($latest === null) {
            return;
        }

        if (Carbon::now()->lessThan(Carbon::parse($latest->blocked_until))) {
            $until = Carbon::parse($latest->blocked_until)->toDateTimeString();
            throw new RuntimeException(
                "Your verification is blocked until {$until}. Please try again later."
            );
        }
    }

    /**
     * Set verified_at and change status to 'verified'.
     */
    private function markVerified(Verification $verification): void
    {
        $verification->update([
            'verified_at' => Carbon::now(),
            'status'      => 'verified',
        ]);
    }

    /**
     * Dispatch the appropriate mail class.
     */
    private function sendMail(User $user, Verification $verification, string $type): void
    {
        if ($type === 'otp') {
            Mail::to($user->email)->send(new OtpMail($verification->code, $verification->purpose));
            return;
        }

        // Retrieve the plain token attached temporarily in createVerification().
        $plain = $verification->getRelation('_plainToken')?->value
            ?? throw new RuntimeException('Plain token missing from verification instance.');

        Mail::to($user->email)->send(
            new VerificationLinkMail($plain, $verification->purpose)
        );
    }

    /**
     * Throw if the verification record is past its expiry time.
     *
     * @throws RuntimeException
     */
    private function assertNotExpired(Verification $verification): void
    {
        if ($verification->isExpired()) {
            $verification->update(['status' => 'expired']);
            throw new RuntimeException('The verification code has expired. Please request a new one.');
        }
    }

    /**
     * Throw if the verification record's user is currently blocked.
     *
     * @throws RuntimeException
     */
    private function assertNotBlocked(Verification $verification): void
    {
        if ($verification->isBlocked()) {
            throw new RuntimeException('Too many attempts. Your verification is temporarily blocked.');
        }
    }

    /**
     * Increment the attempts counter; throw when max attempts reached.
     *
     * @throws RuntimeException
     */
    private function incrementAttempts(Verification $verification): void
    {
        $verification->increment('attempts');
        $verification->refresh();

        $maxAttempts = (int) config('verification.max_attempts', 5);

        if ($verification->attempts >= $maxAttempts) {
            $blockHours   = (int) config('verification.block_hours', 24);
            $blockedUntil = Carbon::now()->addHours($blockHours);

            $verification->update([
                'blocked_until' => $blockedUntil,
                'status'        => 'expired',
            ]);

            throw new RuntimeException(
                "Maximum attempts reached. Verification blocked until {$blockedUntil->toDateTimeString()}."
            );
        }
    }
}
