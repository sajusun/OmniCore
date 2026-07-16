<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebCustomRedirectMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('web')->check() && Auth::guard('web')->user()->status == 'active') {
            $user = Auth::guard('web')->user();

            if ($user->hasAnyRole(['Admin', 'Super Admin'])) {
                return redirect()->intended(route('admin.dashboard', absolute: false));
            }

            // Fallback for other active users if any (e.g. 'User' role)
            Auth::logout();
            return redirect()->route('login')->withErrors(['email' => 'You do not have administrative access.']);
        }

        return redirect()->route('login');
    }
}