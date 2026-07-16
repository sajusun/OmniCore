<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerificationLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The full verification URL built from the plain token.
     */
    public readonly string $verificationUrl;

    /**
     * Create a new VerificationLinkMail instance.
     *
     * @param  string $plainToken  The raw (un-hashed) token to embed in the URL.
     * @param  string $purpose     e.g. 'email_verification' | 'password_reset'
     */
    public function __construct(
        public readonly string $plainToken,
        public readonly string $purpose,
    ) {
        $this->verificationUrl = url(
            route('verification.token.verify', [
                'token'   => $plainToken,
                'purpose' => $purpose,
            ], absolute: false)
        );
    }

    /**
     * Get the message envelope (subject line).
     */
    public function envelope(): Envelope
    {
        $subject = match ($this->purpose) {
            'password_reset'     => 'Reset Your Password',
            'email_verification' => 'Verify Your Email Address',
            default              => 'Verify Your Account',
        };

        return new Envelope(subject: $subject);
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.verification_link',
            with: [
                'verificationUrl' => $this->verificationUrl,
                'purpose'         => $this->purpose,
                'expiryMinutes'   => (int) config('verification.token_expiry_minutes', 60),
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
