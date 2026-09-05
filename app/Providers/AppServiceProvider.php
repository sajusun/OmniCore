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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
    }
}
