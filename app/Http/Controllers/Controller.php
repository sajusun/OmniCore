<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
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

    protected function success($data = null, $message = 'Success', $status = 200)
    {
        return response()->json(['status' => true, 'message' => $message, 'code' => $status, 'data' => $data], $status);
    }

    protected function error($message = 'Something went wrong', $errors = null, $status = 400)
    {
        return response()->json(['status' => false, 'message' => $message, 'code' => $status, 'errors'  => $errors], $status);
    }

    protected function response(bool $status, string $message, int $code, $data = null, bool $paginate = false, $paginateData = null)
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

    protected function makeSlug(string $title, ?Model $model=null): string
    {
        $slug = Str::slug($title);
        if ($model) {
            while ($model::where('slug', $slug)->exists()) {
                $randomString = Str::random(5);
                $slug = Str::slug($title) . '-' . $randomString;
            }
        }

        return $slug;
    }
}
