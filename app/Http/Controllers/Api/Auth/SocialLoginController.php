<?php

namespace App\Http\Controllers\Api\Auth;

use Exception;
use App\Models\User;
use App\Helpers\Helper;
use App\Mail\NewSignUpMail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use App\Services\AppleIdentityTokenService;

class SocialLoginController extends Controller
{
    public array $select;

    public function __construct( private readonly AppleIdentityTokenService $appleService)
    {
        parent::__construct();
        $this->select = ['id', 'name', 'email', 'avatar'];
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
            'token' => 'required',
            'provider' => 'required|in:google,facebook,apple',
        ]);

        try {
            $provider = $request->provider;

            if ($provider === 'apple') {
                // dd($request->email);
                return $this->handleAppleLogin($request->token);
            }
            $socialUser = Socialite::driver($provider)->stateless()->userFromToken($request->token);

            if ($socialUser) {
                $user = User::withTrashed()->where('email', $socialUser->email)->first();
                if (! empty($user->deleted_at)) {
                    return Helper::jsonErrorResponse('Your account has been deleted.', 410);
                }
                $isNewUser = false;

                if (! $user) {
                    $password = Str::random(16);
                    $user = User::create([
                        'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? explode('@', $socialUser->getEmail())[0],
                        'email' => $socialUser->getEmail(),
                        'password' => bcrypt($password),
                        'avatar' => $socialUser->getAvatar(),
                        'status' => 'active',
                    ]);

                    // Mail::to(config('app.support_mail'))->send(new NewSignUpMail($user, $provider));
                }

                Auth::login($user);
                $token = auth('api')->login($user);

                $data = User::select($this->select)->find($user->id);

                return response()->json([
                    'status' => true,
                    'message' => 'User logged in successfully.',
                    'code' => 200,
                    'token_type' => 'bearer',
                    'token' => $token,
                    'data' => $data,
                ], 200);
            } else {
                return Helper::jsonResponse(false, 'Unauthorized', 401);
            }
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }


    private function handleAppleLogin(string $identityToken)
    {
        $payload = $this->appleService->verify($identityToken);

        $email = $payload['email'];

        // Apple hides the email after first sign-in; fall back to apple_id-based
        // synthetic address so updateOrCreate can always match on a stable key.
        $lookupEmail = $email ?? ($payload['apple_id'] . '@privaterelay.appleid.com');

        do {
            $slug = "user_" . rand(1000000000, 9999999999);
        } while (User::where('slug', $slug)->exists());

        $user = User::updateOrCreate(
            ['email' => $lookupEmail],
            [
                'name'             => $this->deriveAppleName($lookupEmail),
                'avatar'           => null,
                'password'         => bcrypt($payload['apple_id']),
                'status'=>'active',
            ]
        );

        $user->update(['is_social_logged' => true]);

        Auth::login($user);
        $jwtToken = auth('api')->login($user);

        $response = [
            'id'       => $user->id,
            'name'     => $user->name,
            'email'    => $user->email,
            'avatar'   => $user->avatar,
            'token'    => $jwtToken,
        ];

        return $this->success($response, 'Successfully Logged In', 200);
    }

    private function deriveAppleName(string $email): string
    {
        $local = explode('@', $email)[0] ?? 'User';

        // Apple private-relay addresses look like random hex strings; use a
        // generic label instead of surfacing the opaque identifier to the UI.
        if (str_ends_with($email, '@privaterelay.appleid.com')) {
            return 'Apple User';
        }

        return ucfirst($local);
    }
}
