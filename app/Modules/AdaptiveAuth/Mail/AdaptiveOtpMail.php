<?php

declare(strict_types=1);

namespace App\Modules\AdaptiveAuth\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdaptiveOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $otpCode,
        public readonly array $deviceInfo,
        public readonly int $expiresInMinutes = 10
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[Action Required] Your Login Verification Code: {$this->otpCode}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'adaptive_auth::emails.device_challenge_otp',
        );
    }
}
