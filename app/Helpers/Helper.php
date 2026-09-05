<?php

namespace App\Helpers;

use App\Services\FileService;
use Exception;
use Illuminate\Contracts\Pagination\CursorPaginator as CursorPaginatorContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator as PaginatorContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\AbstractCursorPaginator;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Str;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class Helper
{
    /**
     * Upload a file using FileService (Supports local, public, s3, etc.).
     */
    public static function fileUpload(mixed $file, string $folder = 'uploads', ?string $name = null, ?string $disk = null): ?string
    {
        return app(FileService::class)->upload($file, $folder, $disk, $name);
    }

    /**
     * Update/Replace an existing file via FileService.
     */
    public static function fileUpdate(mixed $file, string $folder, ?string $oldPath = null, ?string $name = null, ?string $disk = null): ?string
    {
        return app(FileService::class)->replace($file, $oldPath, $folder, $disk, $name);
    }

    /**
     * Delete a file via FileService.
     */
    public static function fileDelete(?string $path, ?string $disk = null): bool
    {
        return app(FileService::class)->delete($path, $disk);
    }

    /**
     * Get accessible URL for a stored file across any storage disk via FileService.
     */
    public static function fileUrl(?string $path, ?string $disk = null): ?string
    {
        return app(FileService::class)->url($path, $disk);
    }

    /**
     * Get image URL with fallback to asset.
     */
    public static function getImageUrl(?string $path, ?string $disk = null): string
    {
        return self::fileUrl($path, $disk) ?? asset('default/placeholder.png');
    }

    /**
     * Generate unique slug for a model.
     */
    public static function makeSlug(mixed $model, string $title): string
    {
        $slug = Str::slug($title);
        while ($model::where('slug', $slug)->exists()) {
            $randomString = Str::random(5);
            $slug = Str::slug($title) . '-' . $randomString;
        }

        return $slug;
    }

    /**
     * Standardized JSON response with auto-pagination detection (Offset & Cursor based).
     *
     * @param bool $status Success/Error indicator
     * @param string $message Friendly status message
     * @param int $code HTTP status code
     * @param mixed $data Data payload or Paginator instance
     * @param bool $paginate Explicit pagination flag
     * @param mixed $paginateData Explicit paginator instance
     * @return JsonResponse
     */
    public static function jsonResponse(
        bool $status = true,
        string $message = 'Success',
        int $code = 200,
        mixed $data = null,
        bool $paginate = false,
        mixed $paginateData = null
    ): JsonResponse {
        $response = [
            'status'  => $status,
            'message' => $message,
            'code'    => $code,
        ];

        // 1. Detect Cursor Paginator (for Infinite Scroll / Mobile Feeds)
        $cursorPaginator = null;
        if ($paginateData instanceof CursorPaginatorContract || $paginateData instanceof AbstractCursorPaginator) {
            $cursorPaginator = $paginateData;
        } elseif ($data instanceof CursorPaginatorContract || $data instanceof AbstractCursorPaginator) {
            $cursorPaginator = $data;
            $data = $data->items();
        } elseif ($data instanceof ResourceCollection && ($data->resource instanceof CursorPaginatorContract || $data->resource instanceof AbstractCursorPaginator)) {
            $cursorPaginator = $data->resource;
        }

        if ($cursorPaginator && method_exists($cursorPaginator, 'cursor')) {
            $response['data'] = $data;
            $response['pagination_type'] = 'cursor';
            $response['cursor'] = [
                'per_page'      => $cursorPaginator->perPage(),
                'next_cursor'   => $cursorPaginator->nextCursor()?->encode(),
                'prev_cursor'   => $cursorPaginator->previousCursor()?->encode(),
                'has_more'      => $cursorPaginator->hasMorePages(),
                'next_page_url' => $cursorPaginator->nextPageUrl(),
                'prev_page_url' => $cursorPaginator->previousPageUrl(),
                'path'          => $cursorPaginator->path(),
            ];

            return response()->json($response, $code);
        }

        // 2. Detect Standard Offset Paginator (LengthAware / Simple)
        $paginator = $paginateData;

        if (!$paginator) {
            if ($data instanceof LengthAwarePaginator || $data instanceof AbstractPaginator || $data instanceof PaginatorContract) {
                $paginator = $data;
                $data = $data->items();
            } elseif ($data instanceof ResourceCollection && ($data->resource instanceof AbstractPaginator || $data->resource instanceof PaginatorContract)) {
                $paginator = $data->resource;
            } elseif ($paginate && is_object($data) && method_exists($data, 'currentPage')) {
                $paginator = $data;
                $data = method_exists($data, 'items') ? $data->items() : $data;
            }
        }

        if ($paginator && method_exists($paginator, 'currentPage')) {
            $response['data'] = $data;
            $response['pagination_type'] = 'offset';
            $response['pagination'] = [
                'current_page'   => $paginator->currentPage(),
                'last_page'      => method_exists($paginator, 'lastPage') ? $paginator->lastPage() : null,
                'per_page'       => $paginator->perPage(),
                'total'          => method_exists($paginator, 'total') ? $paginator->total() : null,
                'first_page_url' => method_exists($paginator, 'url') ? $paginator->url(1) : null,
                'last_page_url'  => method_exists($paginator, 'lastPage') ? $paginator->url($paginator->lastPage()) : null,
                'next_page_url'  => $paginator->nextPageUrl(),
                'prev_page_url'  => $paginator->previousPageUrl(),
                'from'           => method_exists($paginator, 'firstItem') ? $paginator->firstItem() : null,
                'to'             => method_exists($paginator, 'lastItem') ? $paginator->lastItem() : null,
                'path'           => $paginator->path(),
            ];
        } elseif ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    /**
     * Standardized JSON error response.
     */
    public static function jsonErrorResponse(string $message = 'Something went wrong', int $code = 400, mixed $errors = []): JsonResponse
    {
        $response = [
            'status'  => false,
            'message' => $message,
            'code'    => $code,
            'errors'  => is_array($errors) ? $errors : ($errors ? [$errors] : []),
        ];

        return response()->json($response, $code);
    }

    /**
     * Send Push Notification via Firebase.
     */
    public static function sendNotifyMobile(string $token, array $payload): void
    {
        try {
            $factory = (new Factory)->withServiceAccount(storage_path(config('firebase.credentials')));
            $messaging = $factory->createMessaging();
            $notification = Notification::create($payload['title'], Str::limit($payload['body'], 100), $payload['icon']);
            $message = CloudMessage::withTarget('token', $token)->withNotification($notification);
            $messaging->send($message);
        } catch (Exception $exception) {
            \Illuminate\Support\Facades\Log::error($exception->getMessage());
        }
    }
}
