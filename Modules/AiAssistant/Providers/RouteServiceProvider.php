<?php

namespace Modules\AiAssistant\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function map(): void
    {
        $this->mapWebRoutes();
        $this->mapApiRoutes();
    }

    protected function mapWebRoutes(): void
    {
        Route::middleware(config('ai-assistant.routes.middleware', ['web', 'auth']))
            ->prefix(config('ai-assistant.routes.prefix', 'ai-copilot'))
            ->name('ai-copilot.')
            ->group(__DIR__ . '/../Routes/web.php');
    }

    protected function mapApiRoutes(): void
    {
        Route::prefix('api/ai-assistant')
            ->middleware(['api', 'auth:sanctum'])
            ->name('api.ai-assistant.')
            ->group(__DIR__ . '/../Routes/api.php');
    }
}
