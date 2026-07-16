<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOTPMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $otpCode;
    public string $title;

    public function __construct(string $otpCode, string $title)
    {
        $this->otpCode = $otpCode;
        $this->title = $title;
    }

    public function build()
    {
        return $this->subject($this->title)
            ->view('emails.otp')
            ->with([
                'otp' => $this->otpCode,
            ]);
    }
}
