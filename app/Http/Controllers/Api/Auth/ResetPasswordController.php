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
use App\Services\UserService;
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
    public function __construct(private readonly VerificationService $verificationService, private UserService $userService)
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
                return Helper::jsonResponse(true, 'Code Sent Successfully Please Check Your Email.', 200, [
                    "otp" => $verifcation->code
                ]);
            } else {
                return $this->error(message: 'Invalid Email Address', status: 404);
            }
        } catch (Exception $e) {
            return $this->error(message: $e->getMessage(), status: 500);
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
                return $this->error(message: 'User not found', status: 404);
            }

            $verified = $this->verificationService->verifyOtp(user: $user, purpose: Verification::PURPOSE_PASSWORD_RESET, code: (string) $request->input('otp'));

            if (!$verified) {
                return $this->error(message: 'Invalid OTP', status: 400);
            }
            $token = $this->userService->createPasswordResetToken($user);

            return $this->success(message: 'OTP verified successfully.', status: 200, data: [
                'secret_key'   => $token,
            ]);
        } catch (\Exception $e) {
            return $this->error(message: $e->getMessage(), status: 500);
        }
    }

    public function ResetPassword(Request $request)
    {
        $request->validate([
            'email'                 => 'required|email|exists:users,email',
            'secret_key'            => 'required|string',
            'password'              => 'required|string|min:6|confirmed',
        ]);

        try {

            $user = $this->userService->findByEmail($request->email);

            if (!$user) {
                return $this->error(message: 'User not found', status: 404);
            }

            // $resetToken = DB::table('password_reset_tokens')->where('email', $request->email)->first();

            $tokenData = $this->userService->getResetTokenUser($request->secret_key,$request->email);
            if (!$tokenData) {
                return $this->error(message: 'Invalid Secret Key', status: 419);
            }

            // if (!hash_equals($resetToken->token, hash('sha256', $request->secret_key))) {
            //     return $this->error(message: 'Invalid Secret Key', status: 419);
            // }

            // if (Carbon::parse($resetToken->created_at)->addHour()->isPast()) {
            //     return $this->error(message: 'Secret Key expired', status: 419);
            // }

            if ($this->userService->isTokenValid($tokenData)) {
                return $this->error(message: 'Secret Key expired', status: 419);
            }

            $this->userService->updatePassword($user, $request->password);

            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            return $this->success(message: 'Password reset successfully.', status: 200);
        } catch (\Exception $e) {
            return $this->error(message: $e->getMessage(), status: 500);
        }
    }
}
