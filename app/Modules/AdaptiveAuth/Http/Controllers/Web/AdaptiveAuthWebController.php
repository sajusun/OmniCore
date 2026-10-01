<?php

declare(strict_types=1);

namespace App\Modules\AdaptiveAuth\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Modules\AdaptiveAuth\Models\DeviceLoginChallenge;
use App\Modules\AdaptiveAuth\Services\AdaptiveAuthService;
use App\Modules\AdaptiveAuth\Services\TotpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class AdaptiveAuthWebController extends Controller
{
    public function __construct(
        protected AdaptiveAuthService $adaptiveAuth,
        protected TotpService $totp
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
     * Display the TOTP Authenticator Challenge screen during login.
     */
    public function showTotpChallenge(Request $request): View|RedirectResponse
    {
        $userId = $request->session()->get('adaptive_totp_pending_user_id');

        if (!$userId) {
            return redirect()->route('login')->withErrors(['email' => 'No pending authentication session found. Please sign in again.']);
        }

        $userModel = config('auth.providers.users.model', \App\Models\User::class);
        $user = $userModel::find($userId);

        if (!$user) {
            $request->session()->forget(['adaptive_totp_pending_user_id', 'adaptive_totp_remember', 'adaptive_totp_metadata']);
            return redirect()->route('login')->withErrors(['email' => 'User account could not be found.']);
        }

        $deviceMeta = $request->session()->get('adaptive_totp_metadata', []);
        $settings = class_exists(Setting::class) ? Setting::first() : null;

        return view('adaptive_auth::totp_challenge', [
            'user'       => $user,
            'deviceMeta' => $deviceMeta,
            'settings'   => $settings,
        ]);
    }

    /**
     * Verify the submitted 6-digit TOTP code or backup recovery code during login.
     */
    public function verifyTotpChallenge(Request $request): RedirectResponse
    {
        $request->validate([
            'code'            => ['required', 'string'],
            'remember_device' => ['nullable'],
        ]);

        $userId = $request->session()->get('adaptive_totp_pending_user_id');

        if (!$userId) {
            return redirect()->route('login')->withErrors(['email' => 'Authentication session expired. Please sign in again.']);
        }

        $userModel = config('auth.providers.users.model', \App\Models\User::class);
        $user = $userModel::find($userId);

        if (!$user) {
            return redirect()->route('login')->withErrors(['email' => 'User not found.']);
        }

        $code = (string) $request->input('code');
        $valid = $this->totp->verifyUserTotpOrRecovery($user, $code);

        if (!$valid) {
            return back()->withErrors([
                'code' => 'Invalid verification code. Please check your authenticator app or backup recovery code and try again.',
            ])->withInput();
        }

        $remember = (bool) $request->session()->pull('adaptive_totp_remember', false);
        $deviceMeta = $request->session()->pull('adaptive_totp_metadata', []);
        if (empty($deviceMeta)) {
            $deviceMeta = $this->adaptiveAuth->getDetector()->inspect($request);
        }

        $request->session()->forget('adaptive_totp_pending_user_id');

        // Automatically authenticate user into database web session
        Auth::login($user, remember: $remember);
        $request->session()->regenerate();

        $cookie = null;
        if ($request->boolean('remember_device', true)) {
            $device = $this->adaptiveAuth->registerTrustedDevice($user, $deviceMeta);
            $cookie = $this->adaptiveAuth->createDeviceCookie($device->device_uuid);
            $request->session()->put('adaptive_device_uuid', $device->device_uuid);
            $this->adaptiveAuth->logActivity($user, $device, $deviceMeta, 'trusted_login', 'Device verified via TOTP');
        } else {
            $this->adaptiveAuth->logActivity($user, null, $deviceMeta, 'trusted_login', 'Temporary login via TOTP (Not remembered)');
        }

        session()->flash('success', 'Two-Factor Authentication successful! Welcome back.');

        $targetRoute = config('adaptive_auth.redirect_route', 'admin.dashboard');
        $redirectUrl = Route::has($targetRoute) ? route($targetRoute) : url('/dashboard');

        $response = redirect()->intended($redirectUrl);
        if ($cookie) {
            $response->withCookie($cookie);
        }

        return $response;
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

        // Automatically authenticate user into database web session
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
     * Display TOTP MFA Setup (QR Code + Manual Secret).
     */
    public function showTotpSetup(Request $request): View
    {
        $user = $request->user();
        $secretKey = $this->totp->generateSecretKey();
        $qrCodeSvg = $this->totp->getQrCodeSvg($user, $secretKey);

        // Store temporary secret in session
        $request->session()->put('totp_setup_secret', $secretKey);

        return view('adaptive_auth::totp_setup', [
            'user'       => $user,
            'secretKey'  => $secretKey,
            'qrCodeSvg'  => $qrCodeSvg,
            'hasTotp'    => $this->totp->hasTotpEnabled($user),
        ]);
    }

    /**
     * Enable TOTP Authenticator after 6-digit confirmation.
     */
    public function enableTotp(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'code'       => ['required', 'string', 'size:6'],
            'secret_key' => ['nullable', 'string'],
        ]);

        $user = $request->user();
        $secretKey = $request->input('secret_key') ?: (string) $request->session()->get('totp_setup_secret');

        if (!$secretKey) {
            return back()->withErrors(['code' => 'Session expired. Please restart MFA setup.']);
        }

        $result = $this->totp->enableTotp($user, $secretKey, $request->input('code'));

        if (!$result['success']) {
            return back()->withErrors(['code' => $result['message']]);
        }

        $request->session()->forget('totp_setup_secret');

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        return redirect()->route('adaptive.devices.index')
            ->with('success', 'Authenticator App MFA enabled successfully! Save your recovery codes.')
            ->with('recovery_codes', $result['recovery_codes']);
    }

    /**
     * Disable TOTP Authenticator.
     */
    public function disableTotp(Request $request): RedirectResponse
    {
        $user = $request->user();
        $this->totp->disableTotp($user);

        return back()->with('success', 'Two-Factor Authenticator has been disabled.');
    }

    /**
     * Regenerate new backup recovery codes.
     */
    public function regenerateRecoveryCodes(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (!$this->totp->hasTotpEnabled($user)) {
            return back()->with('error', 'Two-Factor Authenticator is not enabled on your account.');
        }

        $newCodes = $this->totp->regenerateRecoveryCodes($user);

        return redirect()->route('adaptive.devices.index')
            ->with('success', 'New emergency backup recovery codes have been generated. Please save or download them immediately!')
            ->with('recovery_codes', $newCodes);
    }

    /**
     * Step-Up Re-Authentication for Sensitive Actions.
     */
    public function confirmStepUp(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = $request->user();

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'The provided password was incorrect.']);
        }

        // Mark sensitive action window as confirmed in session
        $request->session()->put('auth.step_up_confirmed_at', now()->timestamp);

        $intended = $request->session()->pull('url.intended', url()->previous());
        return redirect()->to($intended);
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
            'devices'            => $devices,
            'currentUuid'        => $currentUuid,
            'logs'               => $logs,
            'user'               => $user,
            'hasTotp'            => $this->totp->hasTotpEnabled($user),
            'alwaysRequireTotp'  => $this->totp->alwaysRequiresTotpOnLogin($user),
            'recoveryCodesCount' => $this->totp->getRemainingRecoveryCodesCount($user),
        ]);
    }

    /**
     * Update user's TOTP login preference (Always require TOTP on trusted devices).
     */
    public function updateTotpPreference(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'always_require_on_login' => ['required', 'boolean'],
        ]);

        $user = $request->user();
        $alwaysRequire = $request->boolean('always_require_on_login');

        $this->totp->updateLoginPreference($user, $alwaysRequire);

        $message = $alwaysRequire
            ? 'Two-Factor Authenticator code is now required on EVERY login, even from recognized devices.'
            : 'Two-Factor Authenticator code will only be asked on new or unrecognized devices.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status'                  => true,
                'message'                 => $message,
                'always_require_on_login' => $alwaysRequire,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Revoke access for a specific device.
     */
    public function revoke(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $user = $request->user();
        $revoked = $user->revokeDevice($id);

        if (!$revoked) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Device could not be found or was already revoked.',
                ], 404);
            }
            return back()->with('error', 'Device could not be found or was already revoked.');
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status'  => true,
                'message' => 'Device access has been revoked successfully.',
            ]);
        }

        return back()->with('success', 'Device access has been revoked successfully.');
    }

    /**
     * Revoke access for all other devices except the current session.
     */
    public function revokeOthers(Request $request): RedirectResponse|JsonResponse
    {
        $user = $request->user();
        $cookieName = config('adaptive_auth.cookie_name', 'adaptive_device_token');
        $currentUuid = (string) $request->cookie($cookieName);

        $count = $user->revokeOtherDevices($currentUuid);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status'  => true,
                'message' => "Access revoked from all other devices ({$count} device(s) updated).",
            ]);
        }

        return back()->with('success', "Access revoked from all other devices ({$count} device(s) updated).");
    }

    /**
     * Clear all sign-in audit history logs for the authenticated user.
     */
    public function clearAuditLogs(Request $request): RedirectResponse|JsonResponse
    {
        $user = $request->user();
        $deleted = $user->clearLoginLogs();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status'  => true,
                'message' => "Sign-in audit history cleared successfully ({$deleted} log(s) removed).",
            ]);
        }

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
