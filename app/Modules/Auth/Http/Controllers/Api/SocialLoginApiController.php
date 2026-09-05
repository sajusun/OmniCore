<?php

declare(strict_types=1);

namespace App\Modules\Auth\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AppleIdentityTokenService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginApiController extends Controller
{
    public array $select;

    public function __construct(private readonly AppleIdentityTokenService $appleService)
    {
        parent::__construct();
        $this->select = ['id', 'name', 'email', 'avatar'];
    }

    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        return Socialite::driver($provider)->stateless()->user();
    }

    public function socialLogin(Request $request): JsonResponse
    {
        $request->validate([
            'token'    => 'required',
            'provider' => 'required|in:google,facebook,apple',
        ]);

        try {
            $provider = $request->provider;

            if ($provider === 'apple') {
                return $this->handleAppleLogin($request->token);
            }

            $socialUser = Socialite::driver($provider)->stateless()->userFromToken($request->token);

            if ($socialUser) {
                $user = User::withTrashed()->where('email', $socialUser->email)->first();
                if (!empty($user->deleted_at)) {
                    return $this->error('Your account has been deleted.', null, 410);
                }

                if (!$user) {
                    $password = Str::random(16);
                    $user = User::create([
                        'name'     => $socialUser->getName() ?? $socialUser->getNickname() ?? explode('@', $socialUser->getEmail())[0],
                        'email'    => $socialUser->getEmail(),
                        'password' => bcrypt($password),
                        'avatar'   => $socialUser->getAvatar(),
                        'status'   => 'active',
                    ]);
                }

                Auth::login($user);
                $token = auth('api')->login($user);

                $data = User::select($this->select)->find($user->id);

                return $this->success([
                    'token_type' => 'bearer',
                    'token'      => $token,
                    'user'       => $data,
                    'data'       => $data,
                ], 'User logged in successfully.');
            }

            return $this->error('Unauthorized', null, 401);
        } catch (Exception $e) {
            return $this->error('Something went wrong', ['error' => $e->getMessage()], 500);
        }
    }

    private function handleAppleLogin(string $identityToken): JsonResponse
    {
        $payload = $this->appleService->verify($identityToken);
        $email = $payload['email'];

        $lookupEmail = $email ?? ($payload['apple_id'] . '@privaterelay.appleid.com');

        do {
            $slug = "user_" . rand(1000000000, 9999999999);
        } while (User::where('slug', $slug)->exists());

        $user = User::updateOrCreate(
            ['email' => $lookupEmail],
            [
                'name'     => $this->deriveAppleName($lookupEmail),
                'avatar'   => null,
                'password' => bcrypt($payload['apple_id']),
                'status'   => 'active',
            ]
        );

        $user->update(['is_social_logged' => true]);

        Auth::login($user);
        $jwtToken = auth('api')->login($user);

        $data = User::select($this->select)->find($user->id);

        return $this->success([
            'token_type' => 'bearer',
            'token'      => $jwtToken,
            'user'       => $data,
            'data'       => $data,
        ], 'User logged in successfully.');
    }

    private function deriveAppleName(string $email): string
    {
        $local = explode('@', $email)[0] ?? 'User';

        if (str_ends_with($email, '@privaterelay.appleid.com')) {
            return 'Apple User';
        }

        return ucfirst($local);
    }
}
