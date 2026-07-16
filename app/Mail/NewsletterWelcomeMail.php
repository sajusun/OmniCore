<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsletterWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $url;

    public function __construct($user,$url)
    {
        $this->user = $user;
        $this->url = $url;
    }

    public function build()
    {
        return $this->subject('Welcome! Your Account is Verified')
            ->view('emails.newsletter.welcome')
            ->with([
                'user' => $this->user,
                'url' => $this->url,
            ]);
    }
}
