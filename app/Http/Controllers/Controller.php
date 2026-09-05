<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

abstract class Controller
{
    public function __construct()
    {
        if (!Session::has('locale')) {
            $locale = 'en';
            Session::put('locale', $locale);
            App::setLocale($locale);
        }

        if (!Session::has('timezone')) {
            Session::put('timezone', 'UTC');
        }

        if (auth('web')->check()) {
            auth('web')->user()->update(['last_activity_at' => now()]);
        }

        if (auth('api')->check()) {
            auth('api')->user()->update(['last_activity_at' => now()]);
        }
    }

    /**
     * Standard success JSON response.
     */
    protected function success(mixed $data = null, string $message = 'Success', int $status = 200): JsonResponse
    {
        return Helper::jsonResponse(true, $message, $status, $data);
    }

    /**
     * Standard error JSON response.
     */
    protected function error(string $message = 'Something went wrong', mixed $errors = null, int $status = 400): JsonResponse
    {
        return Helper::jsonErrorResponse($message, $status, $errors ?? []);
    }

    /**
     * Unified JSON response with auto-pagination support.
     */
    protected function response(
        bool $status = true,
        string $message = 'Success',
        int $code = 200,
        mixed $data = null,
        bool $paginate = false,
        mixed $paginateData = null
    ): JsonResponse {
        return Helper::jsonResponse($status, $message, $code, $data, $paginate, $paginateData);
    }

    /**
     * Generate unique slug using Helper.
     */
    protected function makeSlug(string $title, ?Model $model = null): string
    {
        return $model ? Helper::makeSlug($model, $title) : Str::slug($title);
    }
}
