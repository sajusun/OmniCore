<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * AdminMiddleware
 *
 * Protects web admin routes.
 * - Super Admin always passes (wildcard access).
 * - Admin role passes.
 * - Inactive users get a 403 (not a redirect to login).
 * - Unauthenticated users are redirected to login.
 */
class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $guard = Auth::guard('web');

        // Not authenticated → redirect to login
        if (! $guard->check()) {
            return redirect()->route('login');
        }

        $user = $guard->user();

        // Account is inactive → 403
        if ($user->status !== 'active') {
            abort(403, 'Your account is inactive. Please contact the administrator.');
        }

        // Only Super Admin and Admin roles may access the admin area
        if ($user->hasAnyRole(['super_admin', 'admin'])) {
            return $next($request);
        }

        abort(403, 'Unauthorized. You do not have permission to access the admin area.');
    }
}
