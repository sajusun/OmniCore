<?php

declare(strict_types=1);

namespace App\Modules\AdaptiveAuth\Http\Middleware;

use App\Modules\AdaptiveAuth\Models\UserDevice;
use App\Modules\AdaptiveAuth\Services\AdaptiveAuthService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
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

        // Avoid intercepting challenge, login, or logout routes
        if ($request->routeIs('adaptive.challenge*') || $request->routeIs('adaptive.totp*') || $request->routeIs('login') || $request->routeIs('logout')) {
            return $next($request);
        }

        $cookieName = config('adaptive_auth.cookie_name', 'adaptive_device_token');
        $deviceUuid = $request->cookie($cookieName) ?: ($request->hasSession() ? $request->session()->get('adaptive_device_uuid') : null);

        if ($deviceUuid) {
            $device = UserDevice::where('authenticatable_type', $user->getMorphClass())
                ->where('authenticatable_id', $user->getKey())
                ->where('device_uuid', $deviceUuid)
                ->first();

            // Immediate Eviction if this device was explicitly revoked
            if ($device && (!$device->is_trusted || $device->revoked_at !== null)) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $forgetCookie = Cookie::forget($cookieName);

                if ($request->expectsJson()) {
                    return response()->json([
                        'status'  => 'REVOKED',
                        'message' => 'Your session on this device has been revoked.',
                    ], 401)->withCookie($forgetCookie);
                }

                return redirect()->route('login')
                    ->withErrors(['email' => 'Your session on this device was revoked from another device. Please sign in again.'])
                    ->withCookie($forgetCookie);
            }

            // Update activity timestamp periodically (every 5 minutes)
            if ($device && $device->isCurrentlyTrusted()) {
                if (!$device->last_active_at || $device->last_active_at->diffInMinutes(now()) >= 5) {
                    $info = $this->adaptiveAuth->getDetector()->inspect($request);
                    $device->touchActivity($info['ip'], $info['city'] ?? null, $info['country'] ?? null);
                }
            }
        }

        return $next($request);
    }
}
