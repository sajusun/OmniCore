<?php

return [
    'name' => 'AiAssistant',

    /*
    |--------------------------------------------------------------------------
    | Ollama Runtime Connection
    |--------------------------------------------------------------------------
    */
    'ollama' => [
        'base_url' => env('OLLAMA_BASE_URL', 'http://127.0.0.1:11434'),
        'default_model' => env('OLLAMA_DEFAULT_MODEL', 'qwen2.5-coder:7b'),
        'request_timeout' => env('OLLAMA_TIMEOUT', 120),
        'stream_chunk_timeout' => env('OLLAMA_STREAM_TIMEOUT', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Sliding Window & Context Limits
    |--------------------------------------------------------------------------
    */
    'context' => [
        'sliding_window_size' => (int) env('AI_SLIDING_WINDOW_SIZE', 10),
        'max_tokens' => (int) env('AI_MAX_TOKENS', 4096),
    ],

    /*
    |--------------------------------------------------------------------------
    | Route & Access Settings
    |--------------------------------------------------------------------------
    */
    'routes' => [
        'prefix' => 'ai-copilot',
        'middleware' => ['web', 'auth'],
    ],
];
