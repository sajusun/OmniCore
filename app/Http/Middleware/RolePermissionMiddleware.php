<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Exceptions\UnauthorizedException;

/**
 * RolePermissionMiddleware  (alias: 'role_permission')
 *
 * A unified middleware that:
 *  1. Super Admin → bypasses all permission checks (wildcard access).
 *  2. Others      → delegates to Spatie's permission check.
 *
 * Usage in routes:
 *   ->middleware('role_permission:user.create')
 *   ->middleware('role_permission:user.create|user.edit')   // any of these
 */
class RolePermissionMiddleware
{
    /**
     * @param  string  $permission  Pipe-separated list of permission names.
     *                              The user must have AT LEAST ONE of them.
     */
    public function handle(Request $request, Closure $next, string $permission = '')
    {
        // Determine which guard to use
        $guard = $request->is('api/*') ? 'api' : 'web';
        $auth  = Auth::guard($guard);

        if (! $auth->check()) {
            if ($request->expectsJson()) {
                return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        $user = $auth->user();

        // Super Admin bypasses every permission check
        if ($user->hasRole('super_admin')) {
            return $next($request);
        }

        // Inactive account
        if ($user->status !== 'active') {
            if ($request->expectsJson()) {
                return response()->json(['status' => false, 'message' => 'Your account is inactive.'], 403);
            }
            abort(403, 'Your account is inactive.');
        }

        // No permission required → just pass
        if (empty($permission)) {
            return $next($request);
        }

        // Split pipe-separated permissions and check if user has ANY of them
        $requiredPermissions = array_filter(array_map('trim', explode('|', $permission)));

        foreach ($requiredPermissions as $perm) {
            if ($user->can($perm)) {
                return $next($request);
            }
        }

        // None matched → deny
        if ($request->expectsJson()) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthorized. You do not have the required permission.',
            ], 403);
        }

        abort(403, 'Unauthorized. You do not have the required permission.');
    }
}
