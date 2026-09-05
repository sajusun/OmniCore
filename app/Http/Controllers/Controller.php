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
    protected function success(mixed $data = null, string $message = 'Success', int $status = 200, mixed $pagination = null): JsonResponse
    {
        return \App\Helpers\ApiResponse::success($data, $message, $status, $pagination);
    }

    /**
     * Standard error JSON response.
     */
    protected function error(string $message = 'Something went wrong', mixed $errors = null, int $status = 400): JsonResponse
    {
        return \App\Helpers\ApiResponse::error($message, $status, $errors ?? []);
    }

    /**
     * Standard paginated JSON response.
     */
    protected function paginated(mixed $paginator, ?string $resourceClass = null, string $message = 'Data retrieved successfully.', int $code = 200): JsonResponse
    {
        return \App\Helpers\ApiResponse::paginated($paginator, $resourceClass, $message, $code);
    }

    /**
     * Unified JSON response.
     */
    protected function response(
        bool $status = true,
        string $message = 'Success',
        int $code = 200,
        mixed $data = null,
        bool $paginate = false,
        mixed $paginateData = null
    ): JsonResponse {
        return $status
            ? \App\Helpers\ApiResponse::success($data, $message, $code, $paginateData)
            : \App\Helpers\ApiResponse::error($message, $code, $data);
    }

    /**
     * Generate unique slug using Helper.
     */
    protected function makeSlug(string $title, ?Model $model = null): string
    {
        return $model ? Helper::makeSlug($model, $title) : Str::slug($title);
    }
}
