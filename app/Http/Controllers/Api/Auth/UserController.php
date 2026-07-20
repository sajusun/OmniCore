<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public array $select;
    public User $user;

    public function __construct(private UserService $userService)
    {
        parent::__construct();
        $this->user = auth('api')->user();
        $this->select = ['id', 'name', 'email', 'avatar', 'last_activity_at'];
    }

    public function me()
    {
        return $this->success(
            message: 'User details fetched successfully',
            status: 200,
            data: new UserResource($this->user)
        );
    }

    public function onboardingUpdate(Request $request)
    {
        $validatedData = $request->validate([
            'first_name'    => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
            'phone' => 'required|string|numeric|max_digits:20',
            'gender' => 'required|string|max:255',

            'address' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:255',

        ]);

        $user = $this->user;

        if ($request->hasFile('avatar')) {
            if (! empty($user->avatar)) {
                Helper::fileDelete(public_path($user->getRawOriginal('avatar')));
            }
            $validatedData['avatar'] = Helper::fileUpload($request->file('avatar'), 'user/avatar');
        } else {
            $validatedData['avatar'] = $user->avatar;
        }

        $user->update([
            'name' => $validatedData['first_name'] . ' ' . $validatedData['last_name'] ?? '',
            'avatar' => $validatedData['avatar'],
        ]);

        $data=[
            'first_name'    => $validatedData['first_name'],
            'last_name'     => $validatedData['last_name'] ?? null,
            'phone'         => $validatedData['phone'],
            'gender'        => $validatedData['gender'],
            'address'       => $validatedData['address'] ?? null,
            'country'       => $validatedData['country'] ?? null,
            'state'         => $validatedData['state'] ?? null,
            'city'          => $validatedData['city'] ?? null,
            'zip_code'      => $validatedData['zip_code'] ?? null,
        ];

        $user->profile()->updateOrCreate($data);

        return $this->success(message: 'Saved successfully', status: 200, data: new UserResource($user));
    }

    public function updateProfile(Request $request)
    {
        $validatedData = $request->validate([
            'name'      => 'nullable|string|max:100',
            'avatar'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'phone'     => 'nullable|string|numeric|max_digits:20',
            'password'  => 'nullable|string|min:6|confirmed',
            'address'   => 'nullable|string|max:255',
            'country'   => 'nullable|string|max:255',
            'state'     => 'nullable|string|max:255',
            'city'      => 'nullable|string|max:255',
            'zip_code'  => 'nullable|string|max:255',
            'latitude'  => 'nullable|string|max:255',
            'longitude' => 'nullable|string|max:255',

        ]);

        if (! empty($validatedData['password'])) {
            $validatedData['password'] = bcrypt($validatedData['password']);
        } elseif (array_key_exists('password', $validatedData)) {
            unset($validatedData['password']);
        }

        $user = $this->user;

        if ($request->hasFile('avatar')) {
            if (! empty($user->avatar)) {
                Helper::fileDelete(public_path($user->getRawOriginal('avatar')));
            }
            $validatedData['avatar'] = Helper::fileUpload($request->file('avatar'), 'user/avatar');
        } else {
            $validatedData['avatar'] = $user->avatar;
        }

        $user->update($validatedData);

        return $this->success(message: 'Profile updated successfully', status: 200, data: new UserResource($user));
    }

    public function updateAvatar(Request $request)
    {
        $validatedData = $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);
        $user = $this->user;

        if (! empty($user->avatar)) {
            Helper::fileDelete(public_path($user->getRawOriginal('avatar')));
        }
        $validatedData['avatar'] = Helper::fileUpload($request->file('avatar'), 'user/avatar');
        $user->update($validatedData);

        return $this->success(message: 'Avatar updated successfully', status: 200, data: new UserResource($user));
    }

    public function delete()
    {
        $user = $this->user;
        if (! empty($user->avatar) && file_exists(public_path($user->avatar))) {
            Helper::fileDelete(public_path($user->avatar));
        }
        auth('api')->logout();
        $user->delete();

        return $this->success(message: 'Profile deleted successfully', status: 200);
    }

    public function destroy()
    {
        $user = $this->user;
        if (! empty($user->avatar) && file_exists(public_path($user->avatar))) {
            Helper::fileDelete(public_path($user->avatar));
        }
        auth('api')->logout();
        $user->forceDelete();

        return $this->success(message: 'Profile deleted successfully', status: 200);
    }
}
