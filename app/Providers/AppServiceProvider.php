<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;

use Illuminate\Support\ServiceProvider;
use App\Repositories\NotificationRepository;
use App\Repositories\Contracts\NotificationRepositoryInterface;

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

        $this->app->singleton(
            \App\Services\ActivityLogService::class,
            fn() => new \App\Services\ActivityLogService()
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        if (file_exists(app_path('Helpers/helpers.php'))) {
            require_once app_path('Helpers/helpers.php');
        }

        \Illuminate\Support\Facades\Gate::policy(
            \App\Models\ActivityLog::class,
            \App\Policies\ActivityLogPolicy::class
        );
    }
}
