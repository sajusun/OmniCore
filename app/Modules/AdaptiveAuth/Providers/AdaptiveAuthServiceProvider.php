<?php

declare(strict_types=1);

namespace App\Modules\AdaptiveAuth\Providers;

use App\Modules\AdaptiveAuth\Http\Middleware\EnsureDeviceTrusted;
use App\Modules\AdaptiveAuth\Services\AdaptiveAuthService;
use App\Modules\AdaptiveAuth\Services\DeviceDetectorService;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AdaptiveAuthServiceProvider extends ServiceProvider
{
    /**
     * Register services in the container.
     */
    public function register(): void
    {
        // 1. Merge default configuration
        $this->mergeConfigFrom(__DIR__ . '/../Config/adaptive_auth.php', 'adaptive_auth');

        // 2. Register singletons
        $this->app->singleton(DeviceDetectorService::class, function ($app) {
            return new DeviceDetectorService();
        });

        $this->app->singleton(AdaptiveAuthService::class, function ($app) {
            return new AdaptiveAuthService($app->make(DeviceDetectorService::class));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // 1. Load module migrations
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        // 2. Load module blade views (accessible as 'adaptive_auth::challenge' etc.)
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'adaptive_auth');

        // 3. Register middleware alias
        /** @var Router $router */
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('adaptive.device', EnsureDeviceTrusted::class);

        // 4. Register Web Routes
        if (file_exists(__DIR__ . '/../Routes/web.php')) {
            Route::middleware('web')
                ->group(__DIR__ . '/../Routes/web.php');
        }

        // 5. Register API Routes
        if (file_exists(__DIR__ . '/../Routes/api.php')) {
            Route::prefix('api')
                ->middleware('api')
                ->group(__DIR__ . '/../Routes/api.php');
        }

        // 6. Publishable assets if used as vendor package
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../Config/adaptive_auth.php' => config_path('adaptive_auth.php'),
            ], 'adaptive-auth-config');

            $this->publishes([
                __DIR__ . '/../Resources/views' => resource_path('views/vendor/adaptive_auth'),
            ], 'adaptive-auth-views');
        }
    }
}
