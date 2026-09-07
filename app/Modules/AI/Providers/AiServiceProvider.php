<?php

declare(strict_types=1);

namespace App\Modules\AI\Providers;

use App\Modules\AI\Services\AiService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AiServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AiService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Auto-load Migrations
        if (is_dir(__DIR__.'/../Database/Migrations')) {
            $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
        }

        // 2. Auto-load API Routes
        if (file_exists(__DIR__.'/../Routes/api.php')) {
            Route::prefix('api')
                ->middleware('api')
                ->group(__DIR__.'/../Routes/api.php');
        }

        // 3. Auto-load Admin Routes
        if (file_exists(__DIR__.'/../Routes/admin.php')) {
            Route::prefix('admin')
                ->name('admin.')
                ->middleware(['web', 'auth'])
                ->group(__DIR__.'/../Routes/admin.php');
        }

        // 4. Auto-load Views
        if (is_dir(__DIR__.'/../Resources/views')) {
            $this->loadViewsFrom(__DIR__.'/../Resources/views', 'ai');
        }
    }
}
