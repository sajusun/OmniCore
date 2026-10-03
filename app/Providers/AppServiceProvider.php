<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // ─── Enterprise Password Policy (8+ chars, mixed-case, numbers, symbols) ──
        Password::defaults(function () {
            $rule = Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols();

            return app()->isProduction() ? $rule->uncompromised() : $rule;
        });

        // ─── Configurable Rate Limiters ─────────────────────────────────────────
        RateLimiter::for('api', function (Request $request) {
            $limit = config('throttle.api', 60);
            return Limit::perMinute($limit)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('auth-limiter', function (Request $request) {
            $limit = config('throttle.auth', 6);
            return Limit::perMinute($limit)->by($request->ip());
        });

        RateLimiter::for('otp-limiter', function (Request $request) {
            $limit = config('throttle.resend_otp', 3);
            return Limit::perMinute($limit)->by($request->ip());
        });
    }
}
