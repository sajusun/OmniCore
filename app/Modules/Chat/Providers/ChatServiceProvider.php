<?php

namespace App\Modules\Chat\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ChatServiceProvider extends ServiceProvider
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
        // 1. Auto-load Chat Migrations
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        // 2. Auto-load Chat API Routes
        if (file_exists(__DIR__ . '/../Routes/api.php')) {
            Route::prefix('api')
                ->middleware('api')
                ->group(__DIR__ . '/../Routes/api.php');
        }
    }
}
