<?php

namespace App\Modules\AppSupport\Mail;

use App\Modules\AppSupport\Models\AppSupport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppSupportReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public AppSupport $support
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "We received your report [#{$this->support->ticket_no}]: {$this->support->subject}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.app_support_received',
            with: [
                'support' => $this->support,
                'user' => $this->support->user,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
