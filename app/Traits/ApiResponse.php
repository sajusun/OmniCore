<?php

namespace App\Traits;

use App\Helpers\Helper;
use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    public function success(mixed $data = null, ?string $message = 'Success', int $status = 200): JsonResponse
    {
        return Helper::jsonResponse(true, $message ?? 'Success', $status, $data);
    }

    public function error(string $message = 'Something went wrong', mixed $errors = null, int $status = 400): JsonResponse
    {
        return Helper::jsonErrorResponse($message, $status, $errors ?? []);
    }
}
