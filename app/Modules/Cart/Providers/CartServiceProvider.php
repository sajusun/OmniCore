<?php

namespace App\Modules\Cart\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CartServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (is_dir(__DIR__.'/../Database/Migrations')) {
            $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
        }

        if (file_exists(__DIR__.'/../Routes/api.php')) {
            Route::prefix('api')
                ->middleware('api')
                ->group(__DIR__.'/../Routes/api.php');
        }
    }
}
