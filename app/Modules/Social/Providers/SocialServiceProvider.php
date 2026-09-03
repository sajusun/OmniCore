<?php

namespace App\Modules\Social\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class SocialServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Auto-load Module Migrations
        if (is_dir(__DIR__ . '/../Database/Migrations')) {
            $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        }

        // 2. Auto-load Module API Routes
        if (file_exists(__DIR__ . '/../Routes/api.php')) {
            Route::prefix('api')
                ->middleware('api')
                ->group(__DIR__ . '/../Routes/api.php');
        }

        // 3. Auto-load Module Admin Routes (if added in the future)
        if (file_exists(__DIR__ . '/../Routes/admin.php')) {
            Route::prefix('admin')
                ->name('admin.')
                ->middleware(['web', 'auth'])
                ->group(__DIR__ . '/../Routes/admin.php');
        }
    }
}
