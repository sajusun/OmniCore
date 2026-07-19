<?php

namespace App\Http\Controllers\Api\Auth;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Mail\OtpMail;
use App\Helpers\Helper;
use App\Mail\SendOTPMail;
use Illuminate\Support\Str;
use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Services\VerificationService;
use Illuminate\Support\Facades\Validator;

class ResetPasswordController extends Controller
{
    public array $select;
    public function __construct(private readonly VerificationService $verificationService,)
    {
        parent::__construct();
        $this->select = ['id', 'name', 'email', 'avatar'];
    }
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
        try {
            $email = $request->input('email');
            $user  = User::where('email', $email)->first();

            if ($user) {
                $verifcation = $this->verificationService->send(user: $user, purpose: Verification::PURPOSE_PASSWORD_RESET);
                $user->save();
                return Helper::jsonResponse(true, 'Code Sent Successfully Please Check Your Email.', 200, [
                    "otp" => $verifcation->code
                ]);
            } else {
                return Helper::jsonErrorResponse('Invalid Email Address', 404);
            }
        } catch (Exception $e) {
            return Helper::jsonErrorResponse($e->getMessage(), 500);
        }
    }

    public function resetSecretKey(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp'   => 'required|string',
        ]);

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return Helper::jsonErrorResponse('User not found', 404);
            }

            $verified = $this->verificationService->verifyOtp(
                user: $user,
                purpose: Verification::PURPOSE_PASSWORD_RESET,
                code: (string) $request->input('otp'),
            );

            if (!$verified) {
                return Helper::jsonErrorResponse('Invalid OTP', 400);
            }

            $token = Str::random(60);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                [
                    'token'      => hash('sha256', $token),
                    'created_at' => now(),
                ]
            );

            return response()->json([
                'status'  => true,
                'message' => 'OTP verified successfully.',
                'code'    => 200,
                'secret_key'   => $token,
            ]);
        } catch (\Exception $e) {
            return Helper::jsonErrorResponse($e->getMessage(), 500);
        }
    }

    public function ResetPassword(Request $request)
    {
        $request->validate([
            'email'                 => 'required|email|exists:users,email',
            'secret_key'                 => 'required|string',
            'password'              => 'required|string|min:6|confirmed',
        ]);

        try {

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return Helper::jsonErrorResponse('User not found', 404);
            }

            $resetToken = DB::table('password_reset_tokens')->where('email', $request->email)->first();

            if (!$resetToken) {
                return Helper::jsonErrorResponse('Invalid token', 419);
            }

            if (!hash_equals($resetToken->token, hash('sha256', $request->secret_key))) {
                return Helper::jsonErrorResponse('Invalid Secret Key', 419);
            }

            if (Carbon::parse($resetToken->created_at)->addHour()->isPast()) {
                return Helper::jsonErrorResponse('Secret Key expired', 419);
            }

            $user->update(['password' => Hash::make($request->password)]);

            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            return Helper::jsonResponse(true, 'Password reset successfully.', 200);
        } catch (\Exception $e) {
            return Helper::jsonErrorResponse($e->getMessage(), 500);
        }
    }
}
