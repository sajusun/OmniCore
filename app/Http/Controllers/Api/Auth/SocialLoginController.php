<?php

namespace App\Http\Controllers\Api\Auth;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Helpers\Helper;
use App\Models\PostUser;
use App\Mail\NewSignUpMail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    public $select;
    public function __construct()
    {
        parent::__construct();
        $this->select = ['id', 'first_name', 'last_name', 'email', 'avatar'];
    }

    public function RedirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function HandleProviderCallback($provider)
    {
        $data = Socialite::driver($provider)->stateless()->user();
        return $data;
    }

    public function SocialLogin(Request $request)
    {
        $request->validate([
            'token'         => 'required',
            'provider'      => 'required|in:google,facebook,apple',
            // 'role'          => 'required|in:user,trainer',
        ]);

        try {
            $provider   = $request->provider;
            $socialUser = Socialite::driver($provider)->stateless()->userFromToken($request->token);

            if ($socialUser) {
                $user      = PostUser::withTrashed()->where('email', $socialUser->email)->first();
                if (!empty($user->deleted_at)) {
                    return Helper::jsonErrorResponse('Your account has been deleted.', 410);
                }
                $isNewUser = false;

                if (!$user) {
                    $password = Str::random(16);
                    /* if ($request->input('role') == 'trainer') {
                        $status = 'inactive';
                    } else {
                        $status = 'active';
                    } */
                    $user     = PostUser::create([
                        'first_name' => $socialUser->getName() ?? $socialUser->getNickname() ?? explode('@', $socialUser->getEmail())[0],
                        'last_name' =>  null,
                        'email' => $socialUser->getEmail(),
                        'password' => bcrypt($password),
                        'avatar'            => $socialUser->getAvatar(),
                        'otp_expires_at'     => now(),
                        'status'         => true,
                    ]);

                    Mail::to(config('app.support_mail'))->send(new NewSignUpMail($user, $provider));
                }

                Auth::login($user);
                $token = auth('post_user_api')->login($user);

                $data = PostUser::select($this->select)->find($user->id);

                return response()->json([
                    'status'     => true,
                    'message'    => 'User logged in successfully.',
                    'code'       => 200,
                    'token_type' => 'bearer',
                    'token'      => $token,
                    'expires_in' => $user->otp_expires_at,
                    'data'       => $data
                ], 200);
            } else {
                return Helper::jsonResponse(false, 'Unauthorized', 401);
            }
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    //update user type post request
    public function UpdateUserType(Request $request)
    {
        $request->validate([
            'user_type' => 'required|in:tenant,landlord',
        ]);

        try {
            $user = auth('post_user_api')->user();
            $user->update([
                'user_type' => $request->user_type,
            ]);
            $data = PostUser::select($this->select)->find($user->id);
            return Helper::jsonResponse(true, 'User type updated successfully', 200, $data);
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
}
