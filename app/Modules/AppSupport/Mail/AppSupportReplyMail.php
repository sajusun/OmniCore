<?php

namespace App\Modules\AppSupport\Mail;

use App\Modules\AppSupport\Models\AppSupport;
use App\Modules\AppSupport\Models\AppSupportReply;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppSupportReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public AppSupport $support,
        public AppSupportReply $reply
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Admin Update on [#{$this->support->ticket_no}]: {$this->support->subject}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.app_support_reply',
            with: [
                'support' => $this->support,
                'reply'   => $this->reply,
                'user'    => $this->support->user,
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
