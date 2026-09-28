<?php

declare(strict_types=1);

namespace App\Modules\AdaptiveAuth\Mail;

use App\Modules\AdaptiveAuth\Models\UserDevice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewDeviceAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly UserDevice $device,
        public readonly string $userName
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Security Alert: New device logged into your account',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'adaptive_auth::emails.new_device_alert',
        );
    }
}
