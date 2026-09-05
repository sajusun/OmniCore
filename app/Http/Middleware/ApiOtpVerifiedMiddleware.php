<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiOtpVerifiedMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('api')->user();

        if ($user && $user->isEmailVerified()) {
            return $next($request);
        }

        return ApiResponse::error('Email is not verified.', null, 403);
    }
}