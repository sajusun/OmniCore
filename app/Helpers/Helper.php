<?php

namespace App\Helpers;

use Exception;
use Illuminate\Support\Str;
use Kreait\Firebase\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class Helper
{
    public static function fileUpload($file, string $folder, ?string $name = null): ?string
    {
        if (!$file || !$file->isValid()) {
            return null;
        }

        $path = public_path('uploads/' . $folder);

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $fileName = ($name ? Str::slug($name) . '-' : '') . Str::uuid() . '.' . $file->extension();

        $file->move($path, $fileName);

        return 'uploads/' . $folder . '/' . $fileName;
    }

    public static function fileDelete(string $path): void
    {
        if (file_exists($path)) {
            unlink($path);
        }
    }

    public static function makeSlug($model, string $title): string
    {
        $slug = Str::slug($title);
        while ($model::where('slug', $slug)->exists()) {
            $randomString = Str::random(5);
            $slug = Str::slug($title) . '-' . $randomString;
        }

        return $slug;
    }

    public static function jsonResponse(bool $status, string $message, int $code, $data = null, bool $paginate = false, $paginateData = null): JsonResponse
    {
        $response = [
            'status' => $status,
            'message' => $message,
            'code' => $code,
        ];
        if ($paginate && ! empty($paginateData)) {
            $response['data'] = $data;
            $response['pagination'] = [
                'current_page' => $paginateData->currentPage(),
                'last_page' => $paginateData->lastPage(),
                'per_page' => $paginateData->perPage(),
                'total' => $paginateData->total(),
                'first_page_url' => $paginateData->url(1),
                'last_page_url' => $paginateData->url($paginateData->lastPage()),
                'next_page_url' => $paginateData->nextPageUrl(),
                'prev_page_url' => $paginateData->previousPageUrl(),
                'from' => $paginateData->firstItem(),
                'to' => $paginateData->lastItem(),
                'path' => $paginateData->path(),
            ];
        } elseif ($paginate && ! empty($data)) {
            $response['data'] = $data->items();
            $response['pagination'] = [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'first_page_url' => $data->url(1),
                'last_page_url' => $data->url($data->lastPage()),
                'next_page_url' => $data->nextPageUrl(),
                'prev_page_url' => $data->previousPageUrl(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
                'path' => $data->path(),
            ];
        } elseif ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    public static function jsonErrorResponse(string $message, int $code = 400, array $errors = []): JsonResponse
    {
        $response = [
            'status' => false,
            'message' => $message,
            'code' => $code,
            'errors' => $errors,
        ];

        return response()->json($response, $code);
    }

    public static function sendNotifyMobile(string $token, array $payload): void
    {
        try {
            $factory = (new Factory)->withServiceAccount(storage_path(config('firebase.credentials')));
            $messaging = $factory->createMessaging();
            $notification = Notification::create($payload['title'], Str::limit($payload['body'], 100), $payload['icon']);
            $message = CloudMessage::withTarget('token', $token)->withNotification($notification);
            $messaging->send($message);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
        }
    }

    public static function getImageUrl($path): string
    {
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        return asset($path);
    }
}
