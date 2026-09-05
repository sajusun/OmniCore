<?php

declare(strict_types=1);

namespace App\Modules\Auth\Services;

use App\Modules\Auth\Channels\EmailVerificationChannel;
use App\Modules\Auth\Channels\SmsVerificationChannel;
use App\Modules\Auth\Contracts\VerificationChannelInterface;
use App\Modules\Auth\Models\Verification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

class VerificationService
{
    /**
     * Map of supported delivery channels.
     *
     * @var array<string, class-string<VerificationChannelInterface>>
     */
    protected array $channels = [
        'email' => EmailVerificationChannel::class,
        'sms'   => SmsVerificationChannel::class,
    ];

    /**
     * Create and dispatch a new verification for any verifiable Eloquent Model.
     *
     * @param Model $verifiable
     * @param string $purpose
     * @param string|null $type
     * @param string|null $channel
     * @return Verification
     *
     * @throws RuntimeException
     */
    public function send(
        Model $verifiable,
        string $purpose,
        ?string $type = null,
        ?string $channel = null
    ): Verification {
        $type    = $type ?? config('verification.default_type', 'otp');
        $channel = $channel ?? config('verification.default_channel', 'email');

        $this->checkBlocked($verifiable, $purpose);

        return DB::transaction(function () use ($verifiable, $purpose, $type, $channel): Verification {
            $this->expireOld($verifiable, $purpose);

            $verification = $this->createVerification($verifiable, $purpose, $type, $channel);

            $this->deliver($verifiable, $verification, $type, $channel);

            return $verification;
        });
    }

    /**
     * Verify an OTP code submitted for a verifiable model.
     *
     * @param Model $verifiable
     * @param string $purpose
     * @param string $code
     * @return bool
     *
     * @throws RuntimeException
     */
    public function verifyOtp(Model $verifiable, string $purpose, string $code): bool
    {
        $verification = $this->baseQuery($verifiable)
            ->where('purpose', $purpose)
            ->where('verification_type', Verification::TYPE_OTP)
            ->where('status', Verification::STATUS_PENDING)
            ->latest()
            ->first();

        if (!$verification) {
            return false;
        }

        $this->assertNotExpired($verification);
        $this->assertNotBlocked($verification);

        if ($verification->code !== (string) $code) {
            $this->incrementAttempts($verification);
            return false;
        }

        $this->markVerified($verification);
        return true;
    }

    /**
     * Verify a plain-text token received via e-mail link.
     *
     * @param string $purpose
     * @param string $plainToken
     * @return string Redirect URL
     */
    public function verifyToken(string $purpose, string $plainToken): string
    {
        $verification = Verification::query()
            ->where('purpose', $purpose)
            ->where('verification_type', Verification::TYPE_TOKEN)
            ->where('status', Verification::STATUS_PENDING)
            ->latest()
            ->first();

        if (
            $verification === null ||
            $verification->isExpired() ||
            $verification->isBlocked() ||
            !Hash::check($plainToken, $verification->code)
        ) {
            return config('verification.redirects.failed', '/verification/failed');
        }

        $this->markVerified($verification);

        return config('verification.redirects.success', '/verification/success');
    }

    /**
     * Resend a verification (respects cooldown, max requests, and block rules).
     *
     * @param Model $verifiable
     * @param string $purpose
     * @param string|null $type
     * @param string|null $channel
     * @return Verification
     *
     * @throws RuntimeException
     */
    public function resend(
        Model $verifiable,
        string $purpose,
        ?string $type = null,
        ?string $channel = null
    ): Verification {
        $type    = $type ?? config('verification.default_type', 'otp');
        $channel = $channel ?? config('verification.default_channel', 'email');

        $latest = $this->baseQuery($verifiable)
            ->where('purpose', $purpose)
            ->where('status', Verification::STATUS_PENDING)
            ->latest()
            ->first();

        if ($latest !== null) {
            $this->assertNotBlocked($latest);
            $this->assertCooldownPassed($latest);
            $this->assertRequestLimitNotReached($latest);
        }

        return DB::transaction(function () use ($verifiable, $purpose, $type, $channel, $latest): Verification {
            $this->expireOld($verifiable, $purpose);

            $requestCount = $latest ? $latest->request_count + 1 : 1;
            $verification = $this->createVerification($verifiable, $purpose, $type, $channel, $requestCount);

            $this->deliver($verifiable, $verification, $type, $channel);

            return $verification;
        });
    }

    /**
     * Check if a model is verified for a given purpose.
     */
    public function isVerified(Model $verifiable, string $purpose): bool
    {
        return $this->baseQuery($verifiable)
            ->where('purpose', $purpose)
            ->where('status', Verification::STATUS_VERIFIED)
            ->whereNotNull('verified_at')
            ->exists();
    }

    // -------------------------------------------------------------------------
    // Internal Helpers
    // -------------------------------------------------------------------------

    protected function baseQuery(Model $verifiable)
    {
        return Verification::query()
            ->where(function ($q) use ($verifiable) {
                $q->where(function ($sq) use ($verifiable) {
                    $sq->where('verifiable_type', $verifiable->getMorphClass())
                       ->where('verifiable_id', $verifiable->getKey());
                })->orWhere('user_id', $verifiable->getKey());
            });
    }

    protected function createVerification(
        Model $verifiable,
        string $purpose,
        string $type,
        string $channel,
        int $requestCount = 1
    ): Verification {
        $now = Carbon::now();

        if ($type === Verification::TYPE_OTP) {
            $length    = (int) config('verification.otp.length', 4);
            $rawCode   = (string) random_int((int) ('1' . str_repeat('0', $length - 1)), (int) str_repeat('9', $length));
            $savedCode = $rawCode;
            $expiresAt = $now->copy()->addMinutes((int) config('verification.otp.expires_in', 10));
        } else {
            $plainToken = Str::random((int) config('verification.token.length', 64));
            $savedCode  = Hash::make($plainToken);
            $expiresAt  = $now->copy()->addMinutes((int) config('verification.token.expires_in', 60));
        }

        $userId = ($verifiable instanceof \App\Models\User || isset($verifiable->email)) ? $verifiable->getKey() : null;

        $verification = Verification::create([
            'verifiable_type'   => $verifiable->getMorphClass(),
            'verifiable_id'     => $verifiable->getKey(),
            'user_id'           => $userId,
            'verification_type' => $type,
            'channel'           => $channel,
            'purpose'           => $purpose,
            'code'              => $savedCode,
            'attempts'          => 0,
            'request_count'     => $requestCount,
            'last_requested_at' => $now,
            'expires_at'        => $expiresAt,
            'status'            => Verification::STATUS_PENDING,
        ]);

        if ($type === Verification::TYPE_TOKEN) {
            $verification->plain_token = $plainToken ?? null;
        }

        return $verification;
    }

    protected function deliver(Model $verifiable, Verification $verification, string $type, string $channel): void
    {
        $driverClass = $this->channels[$channel] ?? EmailVerificationChannel::class;
        $driver = app($driverClass);
        $driver->send($verifiable, $verification, $type);
    }

    protected function checkBlocked(Model $verifiable, string $purpose): void
    {
        $latest = $this->baseQuery($verifiable)
            ->where('purpose', $purpose)
            ->latest()
            ->first();

        if ($latest !== null) {
            $this->assertNotBlocked($latest);
        }
    }

    protected function expireOld(Model $verifiable, string $purpose): void
    {
        $this->baseQuery($verifiable)
            ->where('purpose', $purpose)
            ->where('status', Verification::STATUS_PENDING)
            ->update(['status' => Verification::STATUS_EXPIRED]);
    }

    protected function markVerified(Verification $verification): void
    {
        $verification->update([
            'status'      => Verification::STATUS_VERIFIED,
            'verified_at' => Carbon::now(),
        ]);
    }

    protected function incrementAttempts(Verification $verification): void
    {
        $maxAttempts = (int) config('verification.otp.max_attempts', 5);
        $attempts    = $verification->attempts + 1;

        if ($attempts >= $maxAttempts) {
            $verification->update([
                'attempts'      => $attempts,
                'status'        => Verification::STATUS_EXPIRED,
                'blocked_until' => Carbon::now()->addMinutes((int) config('verification.rate_limiting.block_duration', 30)),
            ]);

            throw new RuntimeException('Too many invalid attempts. Your verification has been blocked temporarily.');
        }

        $verification->increment('attempts');
    }

    protected function assertNotExpired(Verification $verification): void
    {
        if ($verification->isExpired()) {
            $verification->update(['status' => Verification::STATUS_EXPIRED]);
            throw new RuntimeException('The verification code has expired. Please request a new one.');
        }
    }

    protected function assertNotBlocked(Verification $verification): void
    {
        if ($verification->isBlocked()) {
            $minutes = Carbon::now()->diffInMinutes($verification->blocked_until) + 1;
            throw new RuntimeException("You are temporarily blocked from requesting verifications. Try again in {$minutes} minute(s).");
        }
    }

    protected function assertCooldownPassed(Verification $verification): void
    {
        $cooldown = (int) config('verification.rate_limiting.cooldown_seconds', 60);
        if ($verification->last_requested_at !== null) {
            $secondsSince = Carbon::now()->diffInSeconds($verification->last_requested_at);
            if ($secondsSince < $cooldown) {
                $wait = $cooldown - $secondsSince;
                throw new RuntimeException("Please wait {$wait} second(s) before requesting another code.");
            }
        }
    }

    protected function assertRequestLimitNotReached(Verification $verification): void
    {
        $maxRequests = (int) config('verification.rate_limiting.max_requests', 5);
        if ($verification->request_count >= $maxRequests) {
            $verification->update([
                'blocked_until' => Carbon::now()->addMinutes((int) config('verification.rate_limiting.block_duration', 30)),
            ]);

            throw new RuntimeException('Maximum resend limit reached. You have been blocked temporarily.');
        }
    }
}
