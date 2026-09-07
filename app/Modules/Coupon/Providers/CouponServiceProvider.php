<?php

namespace App\Modules\Coupon\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CouponServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (is_dir(__DIR__.'/../Database/Migrations')) {
            $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
        }

        if (is_dir(__DIR__.'/../Views')) {
            $this->loadViewsFrom(__DIR__.'/../Views', 'coupon');
        }

        if (file_exists(__DIR__.'/../Routes/admin.php')) {
            Route::prefix('admin')
                ->name('admin.')
                ->middleware(['web', 'auth'])
                ->group(__DIR__.'/../Routes/admin.php');
        }

        if (file_exists(__DIR__.'/../Routes/api.php')) {
            Route::prefix('api')
                ->middleware('api')
                ->group(__DIR__.'/../Routes/api.php');
        }
    }
}
