<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new OtpMail instance.
     *
     * @param  string $otp      The plain numeric OTP to display in the email.
     * @param  string $purpose  e.g. 'email_verification' | 'password_reset'
     */
    public function __construct(
        public readonly string $otp,
        public readonly string $purpose,
    ) {}

    /**
     * Get the message envelope (subject line).
     */
    public function envelope(): Envelope
    {
        $subject = match ($this->purpose) {
            'password_reset'     => 'Your Password Reset OTP',
            'email_verification' => 'Verify Your Email Address',
            default              => 'Your One-Time Password',
        };

        return new Envelope(subject: $subject);
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.otp',
            with: [
                'otp'            => $this->otp,
                'purpose'        => $this->purpose,
                'expiryMinutes'  => (int) config('verification.otp_expiry_minutes', 10),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
