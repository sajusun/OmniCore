<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * ApiAdminMiddleware
 *
 * Guards API routes for admin-only access.
 * - Checks auth:api guard.
 * - Super Admin always passes.
 * - Admin role passes.
 * - Inactive users get 403.
 * - Unauthenticated users get 401.
 */
class ApiAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $guard = Auth::guard('api');

        if (! $guard->check()) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $user = $guard->user();

        if ($user->status !== 'active') {
            return response()->json([
                'status'  => false,
                'message' => 'Your account is inactive.',
            ], 403);
        }

        // 'super_admin' and 'admin'
        if ($user->hasAnyRole(['super_admin', 'admin'])) {
            return $next($request);
        }

        return response()->json([
            'status'  => false,
            'message' => 'Unauthorized. Admin access required.',
        ], 403);
    }
}
