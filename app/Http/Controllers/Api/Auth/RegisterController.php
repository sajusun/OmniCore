<?php

namespace App\Http\Controllers\Api\Auth;

use Exception;
use Carbon\Carbon;
use App\Traits\SMS;
use App\Models\User;
use App\Mail\OtpMail;
use App\Helpers\Helper;
use App\Mail\SendOTPMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Events\RegistrationNotificationEvent;
use App\Notifications\RegistrationNotification;

class RegisterController extends Controller
{

    public $select;
    public function __construct()
    {
        parent::__construct();
        $this->select = ['id', 'name', 'email', 'otp', 'avatar', 'last_activity_at'];
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:100',
            'email'      => 'required|string|email|max:150|unique:users',
            'password'   => 'required|string|min:6|confirmed',
            'agree'      => 'required|in:true',
        ]);
        try {
            DB::beginTransaction();
            do {
                $slug = "user_" . rand(1000000000, 9999999999);
            } while (User::where('slug', $slug)->exists());

            $user = User::create([
                'name'               => $request->input('name'),
                'slug'               => $slug,
                'email'              => strtolower($request->input('email')),
                'password'           => Hash::make($request->input('password')),
                'otp'                => rand(10000, 99999),
                'otp_expires_at'     => Carbon::now()->addMinutes(60),
                'status'             => 'active',
                'last_activity_at'   => Carbon::now()
            ]);

            // DB::table('model_has_roles')->insert([
            //     'role_id' => $request->input('role'),
            //     'model_type' => 'App\Models\User',
            //     'model_id' => $user->id
            // ]);

            //notify to admin start
            $notiData = [
                'user_id' => $user->id,
                'title' => 'User register in successfully.',
                'body' => 'User register in successfully.'
            ];

            $admins = User::role('admin', 'web')->get();

            // foreach($admins as $admin){
            //     $admin->notify(new RegistrationNotification($notiData));
            //     if(config('settings.reverb')  === 'on'){
            //         broadcast(new RegistrationNotificationEvent($notiData, $admin->id))->toOthers();
            //     }
            // }
            //notify to admin end

            //$this->twilioSms($phone, 'this sms for testing.');
            //$this->bdSms($phone, 'this sms for testing. thard sms');

            $data = User::select($this->select)->with('roles')->find($user->id);

            Mail::to($user->email)->send(new SendOTPMail($user->otp, 'Verify Your Email Address'));

            DB::commit();

            // $token = auth('api')->login($user);

            return response()->json([
                'status'     => true,
                'message'    => 'User register in successfully.',
                'code'       => 200,
                // 'token_type' => 'bearer',
                // 'token'      => $token,
                'expires_in' => auth('api')->factory()->getTTL() * 60,
                'data' => $data
            ], 200);
        } catch (Exception $e) {

            DB::rollBack();
            Log::info($e->getMessage());
            return Helper::jsonErrorResponse('User registration failed', 500, [$e->getMessage()]);
        }
    }
    public function VerifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp'   => 'required|digits:5',
        ]);
        try {
            $user = User::where('email', $request->input('email'))->first();

            //! Check if email has already been verified
            if (!empty($user->otp_verified_at)) {
                return  Helper::jsonErrorResponse('Email already verified.', 409);
            }

            if ((string)$user->otp !== (string)$request->input('otp')) {
                return Helper::jsonErrorResponse('Invalid OTP code', 422);
            }

            //* Check if OTP has expired
            if (Carbon::parse($user->otp_expires_at)->isPast()) {
                return Helper::jsonErrorResponse('OTP has expired. Please request a new OTP.', 422);
            }

            //* Verify the email
            $user->otp_verified_at   = now();
            $user->otp               = null;
            $user->otp_expires_at    = null;
            $user->save();

            return Helper::jsonResponse(true, 'Email verification successful.', 200,);
        } catch (Exception $e) {
            return Helper::jsonErrorResponse($e->getMessage(), $e->getCode());
        }
    }

    public function ResendOtp(Request $request)
    {

        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        try {
            $user = User::where('email', $request->input('email'))->first();

            if (!$user) {
                return Helper::jsonErrorResponse('User not found.', 404);
            }

            if ($user->otp_verified_at) {
                return Helper::jsonErrorResponse('Email already verified.', 409);
            }

            $newOtp               = rand(1000, 99999);
            $otpExpiresAt         = Carbon::now()->addMinutes(60);
            $user->otp            = $newOtp;
            $user->otp_expires_at = $otpExpiresAt;
            $user->save();

            //* Send the new OTP to the user's email
            Mail::to($user->email)->send(new SendOTPMail($newOtp, 'Verify Your Email Address'));

            return Helper::jsonResponse(true, 'A new OTP has been sent to your email.', 200,[
            "otp"=>$newOtp,
            'expires'=> $otpExpiresAt
            ]);
        } catch (Exception $e) {
            return Helper::jsonErrorResponse($e->getMessage(), 200);
        }
    }
}
