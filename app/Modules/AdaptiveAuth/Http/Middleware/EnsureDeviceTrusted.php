<?php

declare(strict_types=1);

namespace App\Modules\AdaptiveAuth\Http\Middleware;

use App\Modules\AdaptiveAuth\Services\AdaptiveAuthService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDeviceTrusted
{
    public function __construct(
        protected AdaptiveAuthService $adaptiveAuth
    ) {
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!config('adaptive_auth.enabled', true)) {
            return $next($request);
        }

        $user = $request->user();

        // If user is not authenticated, let auth middleware handle it
        if (!$user) {
            return $next($request);
        }

        // Evaluate device trust
        $assessment = $this->adaptiveAuth->evaluateEnvironment($user, $request);

        if ($assessment['status'] === 'challenge_required') {
            // Initiate challenge
            $challenge = $this->adaptiveAuth->createChallenge($user, $assessment['metadata']);

            if ($request->expectsJson()) {
                return response()->json([
                    'status'          => 'CHALLENGE_REQUIRED',
                    'challenge_token' => $challenge->challenge_token,
                    'message'         => 'Verification required for unrecognized device.',
                ], 403);
            }

            // For web, logout temporarily until OTP is verified
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('adaptive.challenge', ['token' => $challenge->challenge_token]);
        }

        $response = $next($request);

        // If a device cookie was generated/refreshed, attach to response
        if (isset($assessment['cookie']) && method_exists($response, 'withCookie')) {
            $response->withCookie($assessment['cookie']);
        }

        return $response;
    }
}
