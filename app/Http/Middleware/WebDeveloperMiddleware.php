<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebDeveloperMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('web')->check() && Auth::guard('web')->user()->hasRole('developer') && Auth::guard('web')->user()->status == 'active') {
            return $next($request);
        }

        abort(403, 'Unauthorized action.');
    }
}