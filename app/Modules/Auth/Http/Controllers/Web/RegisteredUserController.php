<?php

namespace App\Modules\Auth\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Mail\OtpMail;
use App\Modules\Auth\Models\Verification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Exception;
use RuntimeException;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {

        $request->validate([
            'role' => ['nullable', 'exists:roles,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()]
        ]);

        if (config('settings.recaptcha') === 'yes') {
            $request->validate([
                'g-recaptcha-response' => ['required', 'recaptcha'],
            ]);
        }

        $user = User::create([
            'name'     => $request->name,
            'slug'     => User::generateUniqueSlug($request->name),
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $roleId = $request->role ?? \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web'])->id;

        DB::table('model_has_roles')->insert([
            'role_id'    => $roleId,
            'model_type' => 'App\Models\User',
            'model_id'   => $user->id,
        ]);

        // Send OTP verification through standard VerificationService channel
        $user->sendVerification(Verification::PURPOSE_EMAIL_VERIFICATION);

        event(new Registered($user));

        // Bootstrap registration device as trusted
        $adaptiveService = app(\App\Modules\AdaptiveAuth\Services\AdaptiveAuthService::class);
        $bootstrapped = $adaptiveService->bootstrapRegistrationDevice($user, $request);

        session()->put('success', 'Your account has been created successfully. Please verify your email.');

        $response = redirect()->intended(route('verify.otp.page'))->with('email', $request->email);
        if (isset($bootstrapped['cookie'])) {
            $response->withCookie($bootstrapped['cookie']);
        }
        return $response;
    }

    public function otpPage()
    {
        return view('auth.verify-otp');
    }

    public function otpVerify(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'otp'   => ['required', 'string', 'min:4', 'max:10'],
        ]);

        try {
            $user = User::where('email', $request->input('email'))->first();

            if ($user->isEmailVerified()) {
                return back()->with('error', 'Email already verified.');
            }

            // Verify via VerificationService (handles max attempts, lockout, timing-safe compare)
            $verified = $user->verifyOtp((string) $request->input('otp'), Verification::PURPOSE_EMAIL_VERIFICATION);

            if (!$verified) {
                return back()->with('error', 'Invalid OTP code. Please try again.');
            }

            // Refresh/Bootstrap verified registration device as primary trusted device
            $adaptiveService = app(\App\Modules\AdaptiveAuth\Services\AdaptiveAuthService::class);
            $bootstrapped = $adaptiveService->bootstrapRegistrationDevice($user, $request);

            session()->flash('success', 'Email verified successfully. You can now log in.');

            $response = redirect()->route('login');
            if (isset($bootstrapped['cookie'])) {
                $response->withCookie($bootstrapped['cookie']);
            }
            return $response;
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (Exception $e) {
            return back()->with('error', 'Verification failed. Please try again.');
        }
    }

    public function otpResendPage()
    {
        return view('auth.resend-otp');
    }

    public function otpResend(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        try {
            $user = User::where('email', $request->input('email'))->first();

            if (!$user) {
                return back()->with('error', 'User not found.');
            }

            if ($user->isEmailVerified()) {
                return back()->with('error', 'Email already verified.');
            }

            // Resend via VerificationService (respects cooldown, max resend count, block duration)
            $user->resendVerification(Verification::PURPOSE_EMAIL_VERIFICATION);

            session()->flash('success', 'A new verification OTP has been sent to your email.');
            return redirect()->intended(route('verify.otp.page'))->with('email', $request->email);

        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (Exception $e) {
            return back()->with('error', 'Failed to resend OTP. Please try again later.');
        }
    }
}
