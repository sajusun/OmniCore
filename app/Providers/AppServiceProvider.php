<?php

namespace App\Providers;

use App\Repositories\Contracts\NotificationRepositoryInterface;
use App\Repositories\NotificationRepository;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            NotificationRepositoryInterface::class,
            NotificationRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

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
