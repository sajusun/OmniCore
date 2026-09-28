<?php

namespace App\Modules\Auth\Http\Controllers\Web;

use App\Models\Setting;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Auth\LoginRequest;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        $settings = Setting::first();
        return view('auth.login', compact('settings'));
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();
        if ($user->status != 'active' || !$user->hasAnyRole(['admin', 'super_admin'])) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'You do not have administrative access or your account is inactive.',
            ]);
        }

        // Adaptive Device & IP Verification Check
        $adaptiveService = app(\App\Modules\AdaptiveAuth\Services\AdaptiveAuthService::class);
        $assessment = $adaptiveService->evaluateEnvironment($user, $request, isLoginAttempt: true);

        if ($assessment['status'] === 'challenge_required') {
            $challenge = $adaptiveService->createChallenge($user, $assessment['metadata']);
            Auth::logout();
            $request->session()->put('adaptive_challenge_token', $challenge->challenge_token);
            return redirect()->route('adaptive.challenge', ['token' => $challenge->challenge_token]);
        }

        $request->session()->regenerate();

        if (isset($assessment['device'])) {
            $request->session()->put('adaptive_device_uuid', $assessment['device']->device_uuid);
        }

        session()->flash('success', 'Welcome back!');

        $response = redirect()->intended(route('admin.dashboard', absolute: false));
        if (isset($assessment['cookie'])) {
            $response->withCookie($assessment['cookie']);
        }
        return $response;
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        session()->put('success', 'Logout Successfully');

        return redirect('/');
    }
}
