<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

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
