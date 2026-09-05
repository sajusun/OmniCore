<?php

declare(strict_types=1);

namespace App\Modules\Auth\Channels;

use App\Mail\OtpMail;
use App\Mail\VerificationLinkMail;
use App\Modules\Auth\Contracts\VerificationChannelInterface;
use App\Modules\Auth\Models\Verification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class EmailVerificationChannel implements VerificationChannelInterface
{
    public function send(Model $verifiable, Verification $verification, string $type): void
    {
        $email = $verifiable->email ?? null;
        if (!$email) {
            return;
        }

        if ($type === Verification::TYPE_OTP) {
            Mail::to($email)->send(
                new OtpMail($verification->code, $verification->purpose)
            );
        } elseif ($type === Verification::TYPE_TOKEN) {
            $token = $verification->plain_token ?? $verification->code;
            Mail::to($email)->send(
                new VerificationLinkMail($token, $verification->purpose)
            );
        }
    }
}
