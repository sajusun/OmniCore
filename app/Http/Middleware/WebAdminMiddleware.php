<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('web')->check() && Auth::guard('web')->user()->status == 'active') {
            if (Auth::guard('web')->user()->hasAnyRole(['Admin', 'Super Admin'])) {
                return $next($request);
            }
        }

        return redirect()->route('login');
    }
}

