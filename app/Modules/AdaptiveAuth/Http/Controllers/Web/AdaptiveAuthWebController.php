<?php

declare(strict_types=1);

namespace App\Modules\AdaptiveAuth\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Modules\AdaptiveAuth\Models\DeviceLoginChallenge;
use App\Modules\AdaptiveAuth\Services\AdaptiveAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class AdaptiveAuthWebController extends Controller
{
    public function __construct(
        protected AdaptiveAuthService $adaptiveAuth
    ) {
    }

    /**
     * Display the 2FA OTP Challenge screen for unrecognized devices.
     */
    public function showChallenge(Request $request): View|RedirectResponse
    {
        $token = $request->query('token', (string) session('adaptive_challenge_token'));

        if (!$token) {
            return redirect()->route('login')->withErrors(['email' => 'No pending device verification session found.']);
        }

        $challenge = DeviceLoginChallenge::where('challenge_token', $token)->first();

        if (!$challenge || $challenge->isExpired() || $challenge->isVerified()) {
            return redirect()->route('login')->withErrors(['email' => 'Verification session expired. Please sign in again.']);
        }

        $user = $challenge->authenticatable;
        $deviceMeta = $challenge->device_metadata ?? [];

        // Mask email: j***@example.com
        $email = $user->email ?? '';
        $maskedEmail = $this->maskEmail($email);

        $settings = class_exists(Setting::class) ? Setting::first() : null;

        return view('adaptive_auth::challenge', [
            'challengeToken' => $token,
            'maskedEmail'    => $maskedEmail,
            'deviceMeta'     => $deviceMeta,
            'expiresAt'      => $challenge->expires_at->toIso8601String(),
            'secondsLeft'    => max(0, (int) now()->diffInSeconds($challenge->expires_at, false)),
            'settings'       => $settings,
        ]);
    }

    /**
     * Submit and verify the OTP code.
     */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'challenge_token' => ['required', 'string'],
            'otp'             => ['required', 'string', 'min:4', 'max:8'],
        ]);

        $result = $this->adaptiveAuth->verifyChallenge(
            $request->input('challenge_token'),
            $request->input('otp')
        );

        if (!$result['success']) {
            return back()->withErrors([
                'otp' => $result['message'],
            ])->withInput();
        }

        $user = $result['user'];

        // Automatically authenticate user into web session
        Auth::login($user, remember: true);
        $request->session()->regenerate();
        $request->session()->forget('adaptive_challenge_token');

        if (isset($result['device'])) {
            $request->session()->put('adaptive_device_uuid', $result['device']->device_uuid);
        }

        session()->flash('success', 'Device successfully verified! Welcome back.');

        // Determine destination redirect
        $targetRoute = config('adaptive_auth.redirect_route', 'admin.dashboard');
        $redirectUrl = Route::has($targetRoute) ? route($targetRoute) : url('/dashboard');

        return redirect()->intended($redirectUrl)
            ->withCookie($result['cookie']);
    }

    /**
     * Resend verification code.
     */
    public function resend(Request $request): RedirectResponse
    {
        $request->validate([
            'challenge_token' => ['required', 'string'],
        ]);

        $result = $this->adaptiveAuth->resendOtp($request->input('challenge_token'));

        if (!$result['success']) {
            return back()->with('warning', $result['message']);
        }

        return back()->with('success', $result['message']);
    }

    /**
     * Display the Active & Trusted Devices Management Dashboard.
     */
    public function devices(Request $request): View
    {
        $user = $request->user();
        $cookieName = config('adaptive_auth.cookie_name', 'adaptive_device_token');
        $currentUuid = (string) $request->cookie($cookieName);

        $devices = $user->devices()
            ->orderByDesc('last_active_at')
            ->get();

        $logs = $user->loginLogs()
            ->take(15)
            ->get();

        return view('adaptive_auth::devices', [
            'devices'     => $devices,
            'currentUuid' => $currentUuid,
            'logs'        => $logs,
            'user'        => $user,
        ]);
    }

    /**
     * Revoke access for a specific device.
     */
    public function revoke(Request $request, int $id): RedirectResponse
    {
        $user = $request->user();
        $revoked = $user->revokeDevice($id);

        if (!$revoked) {
            return back()->with('error', 'Device could not be found or was already revoked.');
        }

        return back()->with('success', 'Device access has been revoked successfully.');
    }

    /**
     * Revoke access for all other devices except the current session.
     */
    public function revokeOthers(Request $request): RedirectResponse
    {
        $user = $request->user();
        $cookieName = config('adaptive_auth.cookie_name', 'adaptive_device_token');
        $currentUuid = (string) $request->cookie($cookieName);

        $count = $user->revokeOtherDevices($currentUuid);

        return back()->with('success', "Access revoked from all other devices ({$count} device(s) updated).");
    }

    /**
     * Clear all sign-in audit history logs for the authenticated user.
     */
    public function clearAuditLogs(Request $request): RedirectResponse
    {
        $user = $request->user();
        $deleted = $user->clearLoginLogs();

        return back()->with('success', "Sign-in audit history cleared successfully ({$deleted} log(s) removed).");
    }

    /**
     * Helper to mask email address for security display.
     */
    protected function maskEmail(string $email): string
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'your registered email';
        }

        [$name, $domain] = explode('@', $email);
        $visibleLength = min(2, strlen($name));
        $maskedName = substr($name, 0, $visibleLength) . str_repeat('*', max(3, strlen($name) - $visibleLength));

        return $maskedName . '@' . $domain;
    }
}

