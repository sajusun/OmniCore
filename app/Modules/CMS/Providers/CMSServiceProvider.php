<?php

namespace App\Modules\CMS\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CMSServiceProvider extends ServiceProvider
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
        // 1. Auto-load CMS Migrations
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        // 2. Auto-load CMS API Routes
        if (!Route::has('cms.index') && file_exists(__DIR__ . '/../Routes/api.php')) {
            Route::prefix('api')
                ->middleware('api')
                ->group(__DIR__ . '/../Routes/api.php');
        }

        // 3. Auto-load CMS Admin Routes
        if (!Route::has('admin.cms.page.edit') && file_exists(__DIR__ . '/../Routes/admin.php')) {
            Route::prefix('admin')
                ->name('admin.')
                ->middleware(['web', 'auth'])
                ->group(__DIR__ . '/../Routes/admin.php');
        }

        // 4. Auto-load CMS Views ('cms::backend.index')
        if (is_dir(__DIR__ . '/../Views')) {
            $this->loadViewsFrom(__DIR__ . '/../Views', 'cms');
        }
    }
}
