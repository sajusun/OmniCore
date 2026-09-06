<?php

namespace App\Modules\Notification\Providers;

use App\Modules\Notification\Repositories\Contracts\NotificationRepositoryInterface;
use App\Modules\Notification\Repositories\NotificationRepository;
use App\Modules\Notification\Services\BroadcastService;
use App\Modules\Notification\Services\FirebaseService;
use App\Modules\Notification\Services\NotificationService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
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

        // Also bind legacy interface if referenced
        if (interface_exists(\App\Repositories\Contracts\NotificationRepositoryInterface::class)) {
            $this->app->bind(
                \App\Repositories\Contracts\NotificationRepositoryInterface::class,
                NotificationRepository::class
            );
        }

        $this->app->singleton(FirebaseService::class);
        $this->app->singleton(BroadcastService::class);
        $this->app->singleton(NotificationService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Auto-load Notification Migrations
        if (is_dir(__DIR__ . '/../Database/Migrations')) {
            $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        }

        // 2. Auto-load Notification API Routes
        if (file_exists(__DIR__ . '/../Routes/api.php')) {
            Route::prefix('api')
                ->middleware('api')
                ->group(__DIR__ . '/../Routes/api.php');
        }

        // 3. Auto-load Notification Admin Routes
        if (file_exists(__DIR__ . '/../Routes/admin.php')) {
            Route::prefix('admin')
                ->name('admin.')
                ->middleware(['web', 'auth'])
                ->group(__DIR__ . '/../Routes/admin.php');
        }

        // 4. Auto-load Notification Views ('notification::view_name' and legacy alias 'bulk_notification::')
        if (is_dir(__DIR__ . '/../Views')) {
            $this->loadViewsFrom(__DIR__ . '/../Views', 'notification');
            $this->loadViewsFrom(__DIR__ . '/../Views', 'bulk_notification');
        }
    }
}
