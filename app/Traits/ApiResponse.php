<?php

namespace App\Traits;

use App\Helpers\ApiResponse as Responder;
use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    public function success(mixed $data = null, ?string $message = 'Success', int $status = 200, mixed $pagination = null): JsonResponse
    {
        return Responder::success($data, $message ?? 'Success', $status, $pagination);
    }

    public function error(string $message = 'Something went wrong', mixed $errors = null, int $status = 400): JsonResponse
    {
        return Responder::error($message, $status, $errors ?? []);
    }

    public function paginated(mixed $paginator, ?string $resourceClass = null, string $message = 'Data retrieved successfully.', int $code = 200): JsonResponse
    {
        return Responder::paginated($paginator, $resourceClass, $message, $code);
    }
}
