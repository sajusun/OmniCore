<?php

declare(strict_types=1);

namespace App\Modules\AdaptiveAuth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStepUpVerified
{
    /**
     * Handle an incoming request for sensitive actions (e.g., bank/payout changes, security settings).
     */
    public function handle(Request $request, Closure $next, ?string $redirectToRoute = null): Response
    {
        $timeoutMinutes = (int) config('adaptive_auth.step_up.timeout_minutes', 15);
        $lastConfirmedAt = $request->session()->get('auth.step_up_confirmed_at');

        $isFresh = $lastConfirmedAt && (now()->timestamp - $lastConfirmedAt) < ($timeoutMinutes * 60);

        if (!$isFresh) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status'  => 'STEP_UP_REQUIRED',
                    'message' => 'Sensitive action requires recent password or MFA re-confirmation.',
                    'action'  => 'reauthenticate',
                ], 428); // 428 Precondition Required
            }

            // Store intended URL to redirect back after re-confirmation
            $request->session()->put('url.intended', $request->fullUrl());

            $route = $redirectToRoute ?? config('adaptive_auth.step_up.confirm_route', 'password.confirm');
            return redirect()->guest(route($route));
        }

        return $next($request);
    }
}
