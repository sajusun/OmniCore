<?php

namespace App\Http\Controllers\Web\Auth;

use App\Models\Setting;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Middleware\WebCustomRedirectMiddleware;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        $settings = Setting::first();
        return view('auth.login',compact('settings'));
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        session()->put('t-success', 'Welcome back!');

        $user = Auth::user();
        if ($user->status == 'active' && $user->hasAnyRole(['Admin', 'Super Admin'])) {
            return redirect()->intended(route('admin.dashboard', absolute: false));
        }

        Auth::logout();
        return redirect()->route('login')->withErrors([
            'email' => 'You do not have administrative access or your account is inactive.',
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        session()->put('t-success', 'Logout Successfully');

        return redirect('/');
    }
}
