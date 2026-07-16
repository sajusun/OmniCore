<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use App\Helpers\Helper;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public array $select;
    public function __construct()
    {
        parent::__construct();
        $this->select = ['id', 'name', 'email', 'avatar', 'otp_verified_at', 'last_activity_at', 'is_subscribed'];
    }

    public function me()
    {
        $data = User::select($this->select)->find(auth('api')->user()->id);
        $data->package_id = $data->activeSubscription()->value('product_id');
        $data->is_subscribed     = (bool) $data->is_subscribed;

        return Helper::jsonResponse(true, 'User details fetched successfully', 200, $data);
    }

    public function updateProfile(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'phone' => 'required|string|numeric|max_digits:20',
            // 'password' => 'nullable|string|min:6|confirmed',
            // 'address' => 'nullable|string|max:255',
        ]);

        // if (!empty($validatedData['password'])) {
        //     $validatedData['password'] = bcrypt($validatedData['password']);
        // } else if (array_key_exists('password', $validatedData)) {
        //     unset($validatedData['password']);
        // }

        $user = auth('api')->user();

        if ($request->hasFile('avatar')) {
            if (!empty($user->avatar)) {
                Helper::fileDelete(public_path($user->getRawOriginal('avatar')));
            }
            $fileName = Str::uuid() . '.' . $request->file('avatar')->getClientOriginalExtension();

            $validatedData['avatar'] = Helper::fileUpload(
                $request->file('avatar'),
                'user/avatar',
                $fileName
            );
        } else {
            $validatedData['avatar'] = $user->avatar;
        }

        $user->update($validatedData);

        $data = User::select($this->select)->with('roles')->find($user->id);
        $user->logActivity('Profile Update', $user->email . '- update there profile.' . json_encode($request->all()));
        return Helper::jsonResponse(true, 'Profile updated successfully', 200, $data);
    }

    public function updateAvatar(Request $request)
    {
        $validatedData = $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        $user = auth('api')->user();
        if (!empty($user->avatar)) {
            Helper::fileDelete(public_path($user->getRawOriginal('avatar')));
        }
        $fileName = Str::uuid() . '.' . $request->file('avatar')->getClientOriginalExtension();

        $validatedData['avatar'] = Helper::fileUpload(
            $request->file('avatar'),
            'user/avatar',
            $fileName
        );
        $user->update($validatedData);
        $data = User::select($this->select)->find($user->id);
        return Helper::jsonResponse(true, 'Avatar updated successfully', 200, $data);
    }

    public function delete()
    {
        $user = auth('api')->user();

        if (!empty($user->avatar)) {
            Helper::fileDelete(public_path($user->avatar));
        }

        auth('api')->logout();

        $user->delete();

        return Helper::jsonResponse(true, 'Profile deleted successfully', 200);
    }

    public function destroy()
    {
        $user = auth('api')->user();

        if (!empty($user->avatar)) {
            Helper::fileDelete(public_path($user->avatar));
        }

        auth('api')->logout();

        $user->forceDelete();

        return Helper::jsonResponse(true, 'Profile permanently deleted successfully', 200);
    }
}
