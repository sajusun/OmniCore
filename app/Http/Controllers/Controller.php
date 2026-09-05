<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
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
        return ApiResponse::success($data, $message, $status, $pagination);
    }

    /**
     * Standard error JSON response.
     */
    protected function error(string $message = 'Something went wrong', mixed $errors = null, int $status = 400): JsonResponse
    {
        return ApiResponse::error($message, $status, $errors ?? []);
    }

    /**
     * Standard paginated JSON response.
     */
    protected function paginated(mixed $paginator, ?string $resourceClass = null, string $message = 'Data retrieved successfully.', int $code = 200): JsonResponse
    {
        return ApiResponse::paginated($paginator, $resourceClass, $message, $code);
    }

    /**
     * Standard 201 Created JSON response.
     */
    protected function created(mixed $data = null, string $message = 'Resource created successfully.'): JsonResponse
    {
        return ApiResponse::created($data, $message);
    }

    /**
     * Standard 404 Not Found JSON response.
     */
    protected function notFound(string $message = 'Resource not found.'): JsonResponse
    {
        return ApiResponse::notFound($message);
    }

    /**
     * Standard 403 Forbidden JSON response.
     */
    protected function forbidden(string $message = 'Access forbidden.'): JsonResponse
    {
        return ApiResponse::forbidden($message);
    }

    /**
     * Standard 401 Unauthorized JSON response.
     */
    protected function unauthorized(string $message = 'Unauthorized access.'): JsonResponse
    {
        return ApiResponse::unauthorized($message);
    }

    /**
     * Standard 422 Validation Error JSON response.
     */
    protected function validationError(mixed $errors = [], string $message = 'Validation failed.'): JsonResponse
    {
        return ApiResponse::validationError($errors, $message);
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
            ? ApiResponse::success($data, $message, $code, $paginateData)
            : ApiResponse::error($message, $code, $data);
    }

    /**
     * Generate unique slug using Helper.
     */
    protected function makeSlug(string $title, ?Model $model = null): string
    {
        return $model ? Helper::makeSlug($model, $title) : Str::slug($title);
    }
}
