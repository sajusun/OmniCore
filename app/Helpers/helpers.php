<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use App\Services\ActivityLogService;

if (!function_exists('settings')) {
    function settings($key = null)
    {
        $settings = Cache::rememberForever('settings', function () {
            return Setting::first() ?? new Setting();
        });

        if ($key) {
            return $settings->$key ?? null;
        }

        return $settings;
    }
}

if (!function_exists('activity')) {
    /**
     * Get the ActivityLogService instance.
     *
     * @return \App\Services\ActivityLogService
     */
    function activity(): ActivityLogService
    {
        return app(ActivityLogService::class);
    }
}
