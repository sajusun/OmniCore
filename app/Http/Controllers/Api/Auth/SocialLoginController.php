<?php

namespace App\Http\Controllers\Api\Auth;


use App\Models\User;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    // use ApiResponse;
    public function SocialLogin(Request $request, string $provider)
    {
        try {
            if (!in_array($provider, ['google', 'facebook', 'twitter', 'apple'])) {
                return $this->success([], 'Social provider not supported', 200);
            }

            $token = $request->input('token');
            if (!$token) {
                return response()->json(['status' => 'error', 'message' => 'Token is required'], 422);
            }

            $socialUser = Socialite::driver($provider)->stateless()->userFromToken($token);
            $userData = $this->extractUserData($socialUser, $provider);

            do {
                $slug = "user_" . rand(1000000000, 9999999999);
            } while (User::where('slug', $slug)->exists());

            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'slug' => $slug,
                    'provider' => $provider,
                    'avatar' => $socialUser->getAvatar(),
                    'is_social_logged' => true,
                    'provider_id'      => $socialUser->getId(),
                    'google_id'        => $provider === 'google' ? $socialUser->getId() : null,
                    'password' => bcrypt($socialUser->getId()),
                ]
            );

            $user->update([
                'is_social_logged' => true
            ]);

            Auth::login($user);
            $token = auth('api')->login($user);


            $response = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'provider' => $user->provider,
                'token' => $token,
            ];

            return $this->success($response, 'Successfully Logged In', 200);
        } catch (\Exception $e) {
            Log::error("Social Login Error ({$provider}): " . $e->getMessage());
            return $this->error("invalid token", $e->getMessage(), 500);
        }
    }


    private function extractUserData($socialUser, $provider)
    {
        $email = $socialUser->getEmail();

        $name = $socialUser->getName();

        if (empty($name)) {
            $name = $socialUser->user['name']
                ?? explode('@', $email)[0]
                ?? 'User';
        }

        return [
            'email' => $email,
            'name' => $name,
        ];
    }
}
