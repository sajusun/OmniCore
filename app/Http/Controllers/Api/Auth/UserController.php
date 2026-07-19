<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public $select;

    public function __construct()
    {
        parent::__construct();
        $this->select = ['id', 'name', 'email', 'avatar', 'last_activity_at'];
    }

    public function me()
    {
        $data = User::select($this->select)->find(auth('api')->user()->id);

        return Helper::jsonResponse(true, 'User details fetched successfully', 200, $data);
    }

    public function onboardingUpdate(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:100',
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

        $user = auth('api')->user();

        if ($request->hasFile('avatar')) {
            if (! empty($user->avatar)) {
                Helper::fileDelete(public_path($user->getRawOriginal('avatar')));
            }
            $validatedData['avatar'] = Helper::fileUpload($request->file('avatar'), 'user/avatar');
        } else {
            $validatedData['avatar'] = $user->avatar;
        }

        $user->update([
            'name' => $validatedData['first_name'].' '.$validatedData['last_name'] ?? '',
            'avatar' => $validatedData['avatar'],
        ]);
        $user->profile->update([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'] ?? null,
            'phone' => $validatedData['phone'],
            'gender' => $validatedData['gender'],
            'address' => $validatedData['address'] ?? null,
            'country' => $validatedData['country'] ?? null,
            'state' => $validatedData['state'] ?? null,
            'city' => $validatedData['city'] ?? null,
            'zip_code' => $validatedData['zip_code'] ?? null,
        ]);

        $data = User::select($this->select)->find($user->id);

        return Helper::jsonResponse(true, 'Saved successfully', 200, $data);
    }

    public function updateProfile(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'nullable|string|max:100',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'phone' => 'nullable|string|numeric|max_digits:20',
            'password' => 'nullable|string|min:6|confirmed',
            'address' => 'nullable|string|max:255',
        ]);

        if (! empty($validatedData['password'])) {
            $validatedData['password'] = bcrypt($validatedData['password']);
        } elseif (array_key_exists('password', $validatedData)) {
            unset($validatedData['password']);
        }

        $user = auth('api')->user();

        if ($request->hasFile('avatar')) {
            if (! empty($user->avatar)) {
                Helper::fileDelete(public_path($user->getRawOriginal('avatar')));
            }
            $validatedData['avatar'] = Helper::fileUpload($request->file('avatar'), 'user/avatar');
        } else {
            $validatedData['avatar'] = $user->avatar;
        }

        $user->update($validatedData);

        $data = User::select($this->select)->with('roles')->find($user->id);

        return Helper::jsonResponse(true, 'Profile updated successfully', 200, $data);
    }

    public function updateAvatar(Request $request)
    {
        $validatedData = $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);
        $user = auth('api')->user();
        if (! empty($user->avatar)) {
            Helper::fileDelete(public_path($user->getRawOriginal('avatar')));
        }
        $validatedData['avatar'] = Helper::fileUpload($request->file('avatar'), 'user/avatar');
        $user->update($validatedData);
        $data = User::select($this->select)->find($user->id);

        return Helper::jsonResponse(true, 'Avatar updated successfully', 200, $data);
    }

    public function delete()
    {
        $user = User::findOrFail(auth('api')->id());
        if (! empty($user->avatar) && file_exists(public_path($user->avatar))) {
            Helper::fileDelete(public_path($user->avatar));
        }
        Auth::logout('api');
        $user->delete();

        return Helper::jsonResponse(true, 'Profile deleted successfully', 200);
    }

    public function destroy()
    {
        $user = User::findOrFail(auth('api')->id());
        if (! empty($user->avatar) && file_exists(public_path($user->avatar))) {
            Helper::fileDelete(public_path($user->avatar));
        }
        Auth::logout('api');
        $user->forceDelete();

        return Helper::jsonResponse(true, 'Profile deleted successfully', 200);
    }
}
