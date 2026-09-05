<?php

namespace App\Helpers;

use App\Services\FileService;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\AbstractPaginator;
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
     * Standardized JSON response with auto-pagination detection.
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

        // Detect or resolve pagination object
        $paginator = $paginateData;

        if (!$paginator) {
            if ($data instanceof LengthAwarePaginator || $data instanceof AbstractPaginator) {
                $paginator = $data;
                $data = $data->items();
            } elseif ($data instanceof ResourceCollection && $data->resource instanceof AbstractPaginator) {
                $paginator = $data->resource;
            } elseif ($paginate && is_object($data) && method_exists($data, 'currentPage')) {
                $paginator = $data;
                $data = method_exists($data, 'items') ? $data->items() : $data;
            }
        }

        if ($paginator && method_exists($paginator, 'currentPage')) {
            $response['data'] = $data;
            $response['pagination'] = [
                'current_page'   => $paginator->currentPage(),
                'last_page'      => $paginator->lastPage(),
                'per_page'       => $paginator->perPage(),
                'total'          => $paginator->total(),
                'first_page_url' => $paginator->url(1),
                'last_page_url'  => $paginator->url($paginator->lastPage()),
                'next_page_url'  => $paginator->nextPageUrl(),
                'prev_page_url'  => $paginator->previousPageUrl(),
                'from'           => $paginator->firstItem(),
                'to'             => $paginator->lastItem(),
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
