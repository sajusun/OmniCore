<?php

namespace App\Modules\BulkNotification\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminCustomMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $subjectText,
        public string $messageBody,
        public ?string $recipientName = null
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectText,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $viewName = view()->exists('bulk_notification::emails.custom_mail')
            ? 'bulk_notification::emails.custom_mail'
            : 'emails.admin_custom_notification';

        return new Content(
            view: $viewName,
            with: [
                'subjectText'   => $this->subjectText,
                'messageBody'   => $this->messageBody,
                'recipientName' => $this->recipientName ?? 'User',
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
