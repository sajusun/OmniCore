<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

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

    protected function response(bool $status = true, string $message = 'Success', int $code = 200, $data = null, bool $paginate = false, $paginateData = null)
    {
        $response = [
            'status'  => $status,
            'message' => $message,
            'code'    => $code,
        ];

        $paginationObject = $paginateData ?? ($paginate ? $data : null);

        if ($paginate && $paginationObject) {
            $response['data'] = $paginateData ? $data : $paginationObject->items();

            // Pagination metadata mapping
            $response['pagination'] = [
                'current_page'   => $paginationObject->currentPage(),
                'last_page'      => $paginationObject->lastPage(),
                'per_page'       => $paginationObject->perPage(),
                'total'          => $paginationObject->total(),
                'first_page_url' => $paginationObject->url(1),
                'last_page_url'  => $paginationObject->url($paginationObject->lastPage()),
                'next_page_url'  => $paginationObject->nextPageUrl(),
                'prev_page_url'  => $paginationObject->previousPageUrl(),
                'from'           => $paginationObject->firstItem(),
                'to'             => $paginationObject->lastItem(),
                'path'           => $paginationObject->path(),
            ];
        } elseif ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    protected function makeSlug(string $title, ?Model $model = null): string
    {
        $slug = Str::slug($title);
        $randomString = Str::random(5);
        $slug = Str::slug($title) . '-' . $randomString;
        if ($model) {
            while ($model::where('slug', $slug)->exists()) {
                $randomString = Str::random(5);
                $slug = Str::slug($title) . '-' . $randomString;
            }
        }

        return $slug;
    }
}
