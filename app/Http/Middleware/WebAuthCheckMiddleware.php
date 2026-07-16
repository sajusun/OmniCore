<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * WebAuthCheckMiddleware  (alias: 'check')
 *
 * Used on guest-only routes (login, register, etc.).
 * - If authenticated AND active AND is admin/super-admin → redirect to dashboard.
 * - If authenticated but NOT an admin role → logout and let them through
 *   (they have no admin dashboard to land on).
 * - Otherwise (unauthenticated) → pass through to the guest page.
 */
class WebAuthCheckMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $guard = Auth::guard('web');

        if ($guard->check()) {
            $user = $guard->user();

            if ($user->status === 'active' && $user->hasAnyRole(['super_admin', 'admin'])) {
                // Already logged-in admin → send to dashboard
                return redirect()->route('admin.dashboard');
            }

            // Logged in but not an admin (e.g. regular User role) → log them out
            // so they cannot accidentally stay authenticated while viewing admin login.
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return $next($request);
    }
}
