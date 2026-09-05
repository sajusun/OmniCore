<?php

namespace App\Traits;

use App\Helpers\ApiResponse as ResponseHelper;
use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    public function success(mixed $data = null, ?string $message = 'Success', int $status = 200, mixed $pagination = null): JsonResponse
    {
        return ResponseHelper::success($data, $message ?? 'Success', $status, $pagination);
    }

    public function error(string $message = 'Something went wrong', mixed $errors = null, int $status = 400): JsonResponse
    {
        return ResponseHelper::error($message, $status, $errors ?? []);
    }

    public function paginated(mixed $paginator, ?string $resourceClass = null, string $message = 'Data retrieved successfully.', int $code = 200): JsonResponse
    {
        return ResponseHelper::paginated($paginator, $resourceClass, $message, $code);
    }

    public function created(mixed $data = null, string $message = 'Resource created successfully.'): JsonResponse
    {
        return ResponseHelper::created($data, $message);
    }

    public function notFound(string $message = 'Resource not found.'): JsonResponse
    {
        return ResponseHelper::notFound($message);
    }

    public function forbidden(string $message = 'Access forbidden.'): JsonResponse
    {
        return ResponseHelper::forbidden($message);
    }

    public function unauthorized(string $message = 'Unauthorized access.'): JsonResponse
    {
        return ResponseHelper::unauthorized($message);
    }

    public function validationError(mixed $errors = [], string $message = 'Validation failed.'): JsonResponse
    {
        return ResponseHelper::validationError($errors, $message);
    }
}
