<?php

namespace App\Http\Middleware;

use App\Helpers\Helper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebOtpVerifiedMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('web')->user();

        if ($user && $user->isEmailVerified()) {
            return $next($request);
        }

        return Helper::jsonResponse(false, 'Validation failed', 422, 'Email is not verified.');
    }
}