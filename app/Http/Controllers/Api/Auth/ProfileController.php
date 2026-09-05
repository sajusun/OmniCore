<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public array $select;

    public function __construct()
    {
        parent::__construct();
        $this->select = ['id', 'first_name', 'last_name', 'phone', 'gender', 'avatar', 'address', 'country', 'state', 'city', 'zip_code', 'latitude', 'longitude'];
    }

    public function getProfile(): JsonResponse
    {
        $user = auth('api')->user();

        if (!$user) {
            return $this->error('Unauthenticated', null, 401);
        }

        $profile = $user->profile()->select($this->select)->first();

        return $this->success($profile, 'Profile details fetched successfully');
    }

    public function update(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'first_name' => 'nullable|string|max:100',
            'last_name'  => 'nullable|string|max:100',
            'avatar'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'phone'      => 'nullable|string|numeric|max_digits:20',
            'password'   => 'nullable|string|min:6|confirmed',
            'address'    => 'nullable|string|max:255',
            'country'    => 'nullable|string|max:255',
            'state'      => 'nullable|string|max:255',
            'city'       => 'nullable|string|max:255',
            'zip_code'   => 'nullable|string|max:255',
            'latitude'   => 'nullable|string|max:255',
            'longitude'  => 'nullable|string|max:255',
            'bio'        => 'nullable|string|max:255',
            'website'    => 'nullable|string|max:255',
        ]);

        $user = auth('api')->user();

        if (!$user) {
            return $this->error('Unauthenticated', null, 401);
        }

        if (!empty($validatedData['password'])) {
            $validatedData['password'] = bcrypt($validatedData['password']);
        } elseif (array_key_exists('password', $validatedData)) {
            unset($validatedData['password']);
        }

        if ($request->hasFile('avatar')) {
            if (!empty($user->avatar)) {
                Helper::fileDelete(public_path($user->getRawOriginal('avatar')));
            }
            $validatedData['avatar'] = Helper::fileUpload($request->file('avatar'), 'user/avatar');
        } else {
            $validatedData['avatar'] = $user->avatar;
        }

        $user->update($validatedData);

        return $this->success(new UserResource($user), 'Profile updated successfully');
    }
}
