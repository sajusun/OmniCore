<?php

namespace App\Http\Controllers\Api;

use App\Models\ContactUs;
use App\Mail\ContactUsMail;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Mail\ContactUsWelcomeMail;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class ContactController extends Controller
{
    use ApiResponse;

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string|max:2000',
        ]);

        $email = $request->input('email');
        $ip = $request->ip();

        // Rate Limiting: 3 attempts per email per hour
        $emailKey = 'contact:email:' . $email;
        $ipKey = 'contact:ip:' . $ip;

        if (RateLimiter::tooManyAttempts($emailKey, 3)) {
            $seconds = RateLimiter::availableIn($emailKey);
            return $this->error([], "Too many attempts. Try again in " . ceil($seconds / 60) . " minutes.", 429);
        }

        if (RateLimiter::tooManyAttempts($ipKey, 10)) {
            return $this->error([], "Too many attempts from this IP.", 429);
        }

        try {
            // Create contact message
            $contact = ContactUs::create([
                'name' => $request->name,
                'email' => $email,
                'subject' => $request->subject,
                'phone' => $request->phone,
                'message' => $request->message,
                'is_read' => false,
            ]);

            // Send email to admin
            // $adminEmail = env('ADMIN_EMAIL', 'sakhawat.hossain1@softvence.com');
            // Mail::to($adminEmail)->send(new ContactUsMail($contact));
            // sleep(1);
            Mail::mailer('support')->to($contact->email)->send(new ContactUsWelcomeMail($contact));

            // Hit rate limiter
            RateLimiter::hit($emailKey, 3600); // 1 hour
            RateLimiter::hit($ipKey, 3600);

            return $this->success([], 'Your message has been sent successfully! We will get back to you soon.', 201);
        } catch (\Exception $e) {
            Log::error('Contact Us submission failed', [
                'email' => $email,
                'name' => $request->name,
                'error' => $e->getMessage()
            ]);

            return $this->error([], 'Failed to send message. Please try again later.', 500);
        }
    }
}
