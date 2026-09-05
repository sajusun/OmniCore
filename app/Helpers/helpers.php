<?php

use App\Helpers\Helper;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Modules\ActivityLog\Services\ActivityLogService;

/*
|--------------------------------------------------------------------------
| Global Helper Functions
|--------------------------------------------------------------------------
|
| This file contains application-wide helper functions loaded via composer.
| For class-based utility methods, see App\Helpers\Helper.
|
*/

if (!function_exists('settings')) {
    /**
     * Get system setting value or the entire setting model.
     *
     * @param string|null $key
     * @return mixed
     */
    function settings(?string $key = null): mixed
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
     * @return \App\Modules\ActivityLog\Services\ActivityLogService
     */
    function activity(): ActivityLogService
    {
        return app(ActivityLogService::class);
    }
}

if (!function_exists('jsonResponse')) {
    /**
     * Helper proxy for standardized JSON responses.
     */
    function jsonResponse(bool $status, string $message, int $code = 200, $data = null, bool $paginate = false, $paginateData = null): JsonResponse
    {
        return Helper::jsonResponse($status, $message, $code, $data, $paginate, $paginateData);
    }
}

if (!function_exists('jsonErrorResponse')) {
    /**
     * Helper proxy for standardized JSON error responses.
     */
    function jsonErrorResponse(string $message = 'Something went wrong', int $code = 400, mixed $errors = []): JsonResponse
    {
        return Helper::jsonErrorResponse($message, $code, $errors);
    }
}

if (!function_exists('fileService')) {
    /**
     * Get the FileService instance.
     *
     * @return \App\Services\FileService
     */
    function fileService(): \App\Services\FileService
    {
        return app(\App\Services\FileService::class);
    }
}
