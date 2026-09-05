<?php

namespace App\Helpers;

use Illuminate\Contracts\Pagination\CursorPaginator as CursorPaginatorContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator as PaginatorContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\AbstractCursorPaginator;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\CursorPaginator;

class ApiResponse
{
    /**
     * Send a standardized success API response.
     *
     * @param mixed $data Data payload, resource, or paginator
     * @param string $message Friendly success message
     * @param int $code HTTP status code
     * @param mixed $pagination Optional explicit pagination metadata or paginator instance
     * @return JsonResponse
     */
    public static function success(
        mixed $data = null,
        string $message = 'Success',
        int $code = 200,
        mixed $pagination = null
    ): JsonResponse {
        $response = [
            'status' => true,
            'message' => $message,
            'code' => $code,
            'data' => $data,
        ];

        // 1. Auto-detect if $data is a ResourceCollection wrapping a paginator
        if ($data instanceof ResourceCollection && $data->resource instanceof PaginatorContract) {
            $paginator = $data->resource;
            $response['data'] = $data->response()->getData(true)['data'] ?? $data;
            $response['pagination'] = static::extractPaginationMeta($paginator);
            return response()->json($response, $code);
        }

        // 2. Auto-detect if $data itself is a Paginator instance
        if ($data instanceof LengthAwarePaginator || $data instanceof AbstractPaginator) {
            $response['data'] = $data->items();
            $response['pagination'] = static::extractPaginationMeta($data);
            return response()->json($response, $code);
        }

        if ($data instanceof CursorPaginatorContract || $data instanceof AbstractCursorPaginator || $data instanceof CursorPaginator) {
            $response['data'] = $data->items();
            $response['pagination'] = static::extractPaginationMeta($data);
            return response()->json($response, $code);
        }

        // 3. Explicit pagination object passed
        if (!empty($pagination)) {
            if ($pagination instanceof PaginatorContract || $pagination instanceof CursorPaginatorContract) {
                $response['pagination'] = static::extractPaginationMeta($pagination);
            } elseif (is_array($pagination)) {
                $response['pagination'] = $pagination;
            }
        }

        return response()->json($response, $code);
    }

    /**
     * Send a standardized error API response.
     *
     * @param string $message Error description
     * @param int $code HTTP status code
     * @param mixed $errors Detailed field validation errors or exception stack
     * @return JsonResponse
     */
    public static function error(
        string $message = 'Something went wrong',
        int $code = 400,
        mixed $errors = []
    ): JsonResponse {
        $response = [
            'status' => false,
            'message' => $message,
            'code' => $code,
            'errors' => is_array($errors) || is_object($errors) ? $errors : (empty($errors) ? [] : ['error' => $errors]),
        ];

        return response()->json($response, $code);
    }

    /**
     * Send a paginated response with optional resource transformation.
     *
     * @param mixed $paginator Paginator or CursorPaginator instance
     * @param string|null $resourceClass Optional JsonResource class to transform items
     * @param string $message Success message
     * @param int $code HTTP status code
     * @return JsonResponse
     */
    public static function paginated(
        mixed $paginator,
        ?string $resourceClass = null,
        string $message = 'Data retrieved successfully.',
        int $code = 200
    ): JsonResponse {
        $items = $paginator;

        if ($resourceClass && class_exists($resourceClass)) {
            $items = $resourceClass::collection($paginator);
        }

        return static::success($items, $message, $code, $paginator);
    }

    /**
     * Send a 200 OK response.
     */
    public static function ok(mixed $data = null, string $message = 'Success'): JsonResponse
    {
        return static::success($data, $message, 200);
    }

    /**
     * Send a 201 Created response.
     */
    public static function created(mixed $data = null, string $message = 'Resource created successfully.'): JsonResponse
    {
        return static::success($data, $message, 201);
    }

    /**
     * Send a 400 Bad Request error.
     */
    public static function badRequest(string $message = 'Bad Request.', mixed $errors = []): JsonResponse
    {
        return static::error($message, 400, $errors);
    }

    /**
     * Send a 401 Unauthorized error.
     */
    public static function unauthorized(string $message = 'Unauthorized access.'): JsonResponse
    {
        return static::error($message, 401);
    }

    /**
     * Send a 403 Forbidden error.
     */
    public static function forbidden(string $message = 'Access forbidden.'): JsonResponse
    {
        return static::error($message, 403);
    }

    /**
     * Send a 404 Not Found error.
     */
    public static function notFound(string $message = 'Resource not found.'): JsonResponse
    {
        return static::error($message, 404);
    }

    /**
     * Send a 422 Unprocessable Entity (Validation error).
     */
    public static function validationError(mixed $errors = [], string $message = 'Validation failed.'): JsonResponse
    {
        return static::error($message, 422, $errors);
    }

    /**
     * Extract structured pagination metadata supporting both Offset and Cursor pagination.
     */
    public static function extractPaginationMeta(mixed $paginator): array
    {
        // 1. Cursor Pagination
        if ($paginator instanceof CursorPaginatorContract || $paginator instanceof AbstractCursorPaginator || $paginator instanceof CursorPaginator) {
            return [
                'type' => 'cursor',
                'per_page' => $paginator->perPage(),
                'next_cursor' => $paginator->nextCursor()?->encode(),
                'prev_cursor' => $paginator->previousCursor()?->encode(),
                'has_more' => $paginator->hasMorePages(),
                'path' => $paginator->path(),
            ];
        }

        // 2. Length-Aware Offset Pagination
        if ($paginator instanceof LengthAwarePaginator) {
            return [
                'type' => 'offset',
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
                'has_more' => $paginator->hasMorePages(),
                'path' => $paginator->path(),
            ];
        }

        // 3. Simple Offset Pagination
        if ($paginator instanceof PaginatorContract || $paginator instanceof AbstractPaginator) {
            return [
                'type' => 'simple',
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
                'has_more' => $paginator->hasMorePages(),
                'path' => $paginator->path(),
            ];
        }

        return [];
    }
}
