<?php

use App\Helpers\ApiResponse;
use App\Helpers\Helper;
use App\Models\Setting;
use App\Modules\ActivityLog\Services\ActivityLogService;
use App\Services\FileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

/*
|--------------------------------------------------------------------------
| Global Helper Functions
|--------------------------------------------------------------------------
|
| This file contains application-wide helper functions loaded via composer.
| For class-based utility methods, see App\Helpers\Helper.
|
*/

if (! function_exists('settings')) {
    /**
     * Get system setting value or the entire setting model.
     */
    function settings(?string $key = null): mixed
    {
        $settings = Cache::rememberForever('settings', function () {
            return Setting::first() ?? new Setting;
        });

        if ($key) {
            return $settings->$key ?? null;
        }

        return $settings;
    }
}

if (! function_exists('activity')) {
    /**
     * Get the ActivityLogService instance.
     */
    function activity(): ActivityLogService
    {
        return app(ActivityLogService::class);
    }
}

if (! function_exists('api_success')) {
    /**
     * Standardized JSON success response.
     */
    function api_success(mixed $data = null, string $message = 'Success', int $code = 200, mixed $pagination = null): JsonResponse
    {
        return ApiResponse::success($data, $message, $code, $pagination);
    }
}

if (! function_exists('api_error')) {
    /**
     * Standardized JSON error response.
     */
    function api_error(string $message = 'Something went wrong', int $code = 400, mixed $errors = []): JsonResponse
    {
        return ApiResponse::error($message, $code, $errors);
    }
}

if (! function_exists('api_response')) {
    /**
     * Standardized JSON response based on boolean status.
     */
    function api_response(bool $status, string $message, int $code = 200, mixed $data = null): JsonResponse
    {
        return $status
            ? ApiResponse::success($data, $message, $code)
            : ApiResponse::error($message, $code, $data);
    }
}

if (! function_exists('jsonResponse')) {
    /**
     * Helper proxy for standardized JSON responses (legacy alias).
     */
    function jsonResponse(bool $status, string $message, int $code = 200, $data = null, bool $paginate = false, $paginateData = null): JsonResponse
    {
        return Helper::jsonResponse($status, $message, $code, $data, $paginate, $paginateData);
    }
}

if (! function_exists('jsonErrorResponse')) {
    /**
     * Helper proxy for standardized JSON error responses (legacy alias).
     */
    function jsonErrorResponse(string $message = 'Something went wrong', int $code = 400, mixed $errors = []): JsonResponse
    {
        return Helper::jsonErrorResponse($message, $code, $errors);
    }
}

if (! function_exists('fileService')) {
    /**
     * Get the FileService instance.
     */
    function fileService(): FileService
    {
        return app(FileService::class);
    }
}
