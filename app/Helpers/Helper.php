<?php

namespace App\Helpers;

use App\Services\FileService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
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
            $slug = Str::slug($title).'-'.$randomString;
        }

        return $slug;
    }

    /**
     * Standardized JSON response proxying to ApiResponse.
     */
    public static function jsonResponse(
        bool $status = true,
        string $message = 'Success',
        int $code = 200,
        mixed $data = null,
        bool $paginate = false,
        mixed $paginateData = null
    ): JsonResponse {
        if (! $status) {
            return ApiResponse::error($message, $code, $data);
        }

        return ApiResponse::success($data, $message, $code, $paginateData);
    }

    /**
     * Standardized JSON error response proxying to ApiResponse.
     */
    public static function jsonErrorResponse(string $message = 'Something went wrong', int $code = 400, mixed $errors = []): JsonResponse
    {
        return ApiResponse::error($message, $code, $errors);
    }

    /**
     * Send Push Notification via Firebase.
     */
    public static function sendNotifyMobile(string $token, array $payload): void
    {
        try {
            $factory = (new Factory)->withServiceAccount(storage_path((string) config('firebase.credentials')));
            $messaging = $factory->createMessaging();
            $message = CloudMessage::fromArray([
                'token' => $token,
                'notification' => [
                    'title' => $payload['title'] ?? '',
                    'body' => Str::limit($payload['body'] ?? '', 100),
                    'image' => $payload['icon'] ?? null,
                ],
            ]);
            $messaging->send($message);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
        }
    }
}
