<?php

declare(strict_types=1);

namespace App\Modules\Auth\Providers;

use App\Modules\Auth\Services\VerificationService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // 1. Merge default module configuration
        $this->mergeConfigFrom(__DIR__ . '/../Config/verification.php', 'verification');

        // 2. Register VerificationService singleton
        $this->app->singleton(VerificationService::class, function ($app) {
            return new VerificationService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Load module migrations
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        // 2. Load module blade views (accessible via 'auth::view-name' or default fallback)
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'auth');

        // 3. Load API routes
        if (file_exists(__DIR__ . '/../Routes/api.php')) {
            Route::prefix('api')
                ->middleware('api')
                ->group(__DIR__ . '/../Routes/api.php');
        }

        // 4. Load Web routes
        if (file_exists(__DIR__ . '/../Routes/web.php')) {
            Route::middleware('web')
                ->group(__DIR__ . '/../Routes/web.php');
        }
    }
}
