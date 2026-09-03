<?php

namespace App\Modules\AppSupport\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppSupportServiceProvider extends ServiceProvider
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
        // 1. Auto-load Module Migrations (Plug-and-play migration discovery)
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        // 2. Auto-load Module API Routes (if not manually included)
        if (!Route::has('app-support.categories') && file_exists(__DIR__ . '/../Routes/api.php')) {
            Route::prefix('api')
                ->middleware('api')
                ->group(__DIR__ . '/../Routes/api.php');
        }

        // 3. Auto-load Module Admin Routes (if not manually included)
        if (!Route::has('admin.app-supports.index') && file_exists(__DIR__ . '/../Routes/admin.php')) {
            Route::prefix('admin')
                ->name('admin.')
                ->middleware(['web', 'auth'])
                ->group(__DIR__ . '/../Routes/admin.php');
        }
    }
}
