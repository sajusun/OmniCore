<?php

namespace App\Http\Controllers\Web\Auth;

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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Middleware\WebCustomRedirectMiddleware;
use Exception;

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
            'role' => ['required', 'exists:roles,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()]
        ]);

        if (config('settings.recaptcha') === 'yes') {
            $request->validate([
                'g-recaptcha-response' => ['required', 'recaptcha'],
            ]);
        }

        do {
            $slug = "user_".rand(1000000000, 9999999999);
        } while (User::where('slug', $slug)->exists());

        $user = User::create([
            'name'           => $request->name,
            'slug'           => $slug,
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'otp'            => rand(1000, 9999),
            'otp_expires_at' => Carbon::now()->addMinutes(60),
        ]);

        DB::table('model_has_roles')->insert([
            'role_id' => $request->role,
            'model_type' => 'App\Models\User',
            'model_id' => $user->id
        ]);

        //* Send the new OTP to the user's email
        Mail::to($user->email)->send(new OtpMail($user->otp, $user, 'Verify Your Email Address'));

        event(new Registered($user));

        //Auth::login($user);

        session()->put('t-success', 'Your account has been created successfully. Please verify your email.');

        return redirect()->intended(route('verify.otp.page'))->with('email', $request->email);
    }

    public function otpPage(){
        return view('auth.verify-otp');
    }

    public function otpVerify(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp'   => 'required|digits:4',
        ]);
        try {
            $user = User::where('email', $request->input('email'))->first();

            //! Check if email has already been verified
            if (!empty($user->otp_verified_at)) {
                return back()->with('t-error', 'Email already verified.');
            }

            if ((string)$user->otp !== (string)$request->input('otp')) {
                return back()->with('t-error', 'Invalid OTP.');
            }

            //* Check if OTP has expired
            if (Carbon::parse($user->otp_expires_at)->isPast()) {
                return back()->with('t-error', 'OTP has expired.');
            }

            //* Verify the email
            $user->otp_verified_at   = now();
            $user->otp               = null;
            $user->otp_expires_at    = null;
            $user->save();

            return redirect()->intended(route('login'));
        } catch (Exception $e) {
            return back()->with('t-error', $e->getMessage());
        }
    }

    public function otpResendPage(){
        return view('auth.resend-otp');
    }

    public function otpResend(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        try {
            $user = User::where('email', $request->input('email'))->first();

            if (!$user) {
                return back()->with('t-error', 'User not found.');
            }

            if ($user->otp_verified_at) {
                return back()->with('t-error', 'Email already verified.');
            }

            $newOtp               = rand(1000, 9999);
            $otpExpiresAt         = Carbon::now()->addMinutes(60);
            $user->otp            = $newOtp;
            $user->otp_expires_at = $otpExpiresAt;
            $user->save();

            //* Send the new OTP to the user's email
            Mail::to($user->email)->send(new OtpMail($newOtp, $user, 'Verify Your Email Address'));

            return redirect()->intended(route('verify.otp.page'))->with('email', $request->email);

        } catch (Exception $e) {
            return back()->with('t-error', $e->getMessage());
        }
    }
}
