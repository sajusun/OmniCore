<?php

namespace App\Http\Controllers\Api\Auth;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Mail\OtpMail;
use App\Helpers\Helper;
use App\Mail\SendOTPMail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ResetPasswordController extends Controller
{
    public $select;
    public function __construct()
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
            $otp   = rand(10000, 99999);
            $user  = User::where('email', $email)->first();

            if ($user) {
                Mail::to($email)->send(new SendOTPMail($otp, 'Reset Your Password'));

                $user->otp            = $otp;
                $user->otp_expires_at = Carbon::now()->addMinutes(60);
                $user->save();

                return Helper::jsonResponse(true, 'OTP Code Sent Successfully Please Check Your Email.', 200,[
                    "otp"=>$otp
                ]);
            } else {
                return Helper::jsonErrorResponse('Invalid Email Address', 404);
            }
        } catch (Exception $e) {
            return Helper::jsonErrorResponse($e->getMessage(), 500);
        }
    }

    public function MakeOtpToken(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp'   => 'required|digits:5',
        ]);

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return Helper::jsonErrorResponse('User not found', 404);
            }

            if (Carbon::parse($user->otp_expires_at)->isPast()) {
                return Helper::jsonErrorResponse('OTP has expired.', 400);
            }

            if ($user->otp !== $request->otp) {
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

            $user->update([
                'otp'            => null,
                'otp_expires_at' => null,
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'OTP verified successfully.',
                'code'    => 200,
                'token'   => $token,
            ]);
        } catch (\Exception $e) {
            return Helper::jsonErrorResponse($e->getMessage(), 500);
        }
    }

    public function ResetPassword(Request $request)
    {
        $request->validate([
            'email'                 => 'required|email|exists:users,email',
            'token'                 => 'required|string',
            'password'              => 'required|string|min:6|confirmed',
        ]);

        try {

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return Helper::jsonErrorResponse('User not found', 404);
            }

            $resetToken = DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->first();

            if (!$resetToken) {
                return Helper::jsonErrorResponse('Invalid token', 419);
            }

            if (
                !hash_equals(
                    $resetToken->token,
                    hash('sha256', $request->token)
                )
            ) {
                return Helper::jsonErrorResponse('Invalid token', 419);
            }

            if (
                Carbon::parse($resetToken->created_at)
                ->addHour()
                ->isPast()
            ) {
                return Helper::jsonErrorResponse('Token expired', 419);
            }

            $user->update([
                'password' => Hash::make($request->password),
            ]);

            DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->delete();

            return Helper::jsonResponse(
                true,
                'Password reset successfully.',
                200
            );
        } catch (\Exception $e) {
            return Helper::jsonErrorResponse($e->getMessage(), 500);
        }
    }
}
