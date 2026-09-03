<?php

namespace App\Modules\ActivityLog\Providers;

use App\Modules\ActivityLog\Models\ActivityLog;
use App\Modules\ActivityLog\Policies\ActivityLogPolicy;
use App\Modules\ActivityLog\Services\ActivityLogService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ActivityLogServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            ActivityLogService::class,
            fn() => new ActivityLogService()
        );

        // Alias for backwards compatibility if app('App\Services\ActivityLogService') is called
        $this->app->alias(ActivityLogService::class, \App\Services\ActivityLogService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Auto-load ActivityLog Migrations
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        // 2. Auto-load ActivityLog Admin Routes
        if (!Route::has('admin.activity-logs.index') && file_exists(__DIR__ . '/../Routes/admin.php')) {
            Route::prefix('admin')
                ->name('admin.')
                ->middleware(['web', 'auth'])
                ->group(__DIR__ . '/../Routes/admin.php');
        }

        // 3. Auto-load ActivityLog Views ('activity_log::backend.*')
        if (is_dir(__DIR__ . '/../Views')) {
            $this->loadViewsFrom(__DIR__ . '/../Views', 'activity_log');
        }

        // 4. Register Policy
        Gate::policy(ActivityLog::class, ActivityLogPolicy::class);
        Gate::policy(\App\Models\ActivityLog::class, ActivityLogPolicy::class);
    }
}
