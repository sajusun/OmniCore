<?php

namespace App\Http\Controllers\Api;

use App\Models\Subscriber;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Mail\NewsletterWelcomeMail;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class NewsletterController extends Controller
{
    use ApiResponse;

    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $email = $request->input('email');
        $ip = $request->ip();

        // Rate Limiting: 3 attempts per email per hour
        $emailKey = 'newsletter:email:' . $email;
        $ipKey = 'newsletter:ip:' . $ip;

        if (RateLimiter::tooManyAttempts($emailKey, 3)) {
            $seconds = RateLimiter::availableIn($emailKey);
            return $this->error([], "Too many attempts. Try again in " . ceil($seconds / 60) . " minutes.", 429);
        }

        if (RateLimiter::tooManyAttempts($ipKey, 10)) {
            return $this->error([], "Too many attempts from this IP.", 429);
        }

        // Check if already subscribed
        $existing = Subscriber::where('email', $email)->first();
        if ($existing) {
            return $this->error([], "You're already subscribed to our newsletter!", 409);
        }

        try {
            // Create subscriber - directly verified
            Subscriber::create([
                'email' => $email,
                'ip_address' => $ip,
                'user_agent' => $request->userAgent(),
                'is_verified' => true,
                'verified_at' => now(),
            ]);

            // Send welcome email
            Mail::to($email)->send(new NewsletterWelcomeMail($email));

            // Hit rate limiter
            RateLimiter::hit($emailKey, 3600);
            RateLimiter::hit($ipKey, 3600);

            return $this->success([], 'Successfully subscribed! Check your email for a welcome message.', 201);
        } catch (\Exception $e) {
            Log::error('Newsletter subscription failed', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);

            return $this->error([], 'Failed to subscribe. Please try again later.', 500);
        }
    }

    // Optional: Unsubscribe endpoint
    public function unsubscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $subscriber = Subscriber::where('email', $request->email)->first();

        if (!$subscriber) {
            return $this->error([], 'Email not found in our subscriber list.', 404);
        }

        $subscriber->delete();

        return $this->success([], 'Successfully unsubscribed from newsletter.', 200);
    }
}
