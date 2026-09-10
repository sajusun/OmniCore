<?php

namespace Modules\AiAssistant\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\AiAssistant\Services\OllamaStreamService;

class AiAssistantServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'AiAssistant';
    protected string $moduleNameLower = 'aiassistant';

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/ai-assistant.php',
            'ai-assistant'
        );

        $this->app->singleton(OllamaStreamService::class, function ($app) {
            return new OllamaStreamService(
                baseUrl: config('ai-assistant.ollama.base_url', 'http://127.0.0.1:11434'),
                defaultModel: config('ai-assistant.ollama.default_model', 'qwen2.5-coder:7b'),
                timeout: (int) config('ai-assistant.ollama.request_timeout', 120)
            );
        });
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'aiassistant');
    }
}
