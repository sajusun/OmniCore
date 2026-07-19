<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public array $select;

    private User $user;

    public function __construct()
    {
        parent::__construct();
        $this->user = auth('api')->user();
        $this->select = ['id', 'first_name', 'last_name', 'phone', 'gender', 'avatar', 'address', 'country', 'state', 'city', 'zip_code', 'latitude', 'longitude'];
    }

    public function getProfile()
    {
        $profile = $this->user->profile()->select($this->select)->first();

        return Helper::jsonResponse(true, 'Profile details fetched successfully', 200, $profile);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'phone' => 'nullable|string|numeric|max_digits:20',
            'password' => 'nullable|string|min:6|confirmed',
            'address' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:255',
            'latitude' => 'nullable|string|max:255',
            'longitude' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
        ]);

        if (! empty($validatedData['password'])) {
            $validatedData['password'] = bcrypt($validatedData['password']);
        } elseif (array_key_exists('password', $validatedData)) {
            unset($validatedData['password']);
        }

        if ($request->hasFile('avatar')) {
            if (! empty($this->user->avatar)) {
                Helper::fileDelete(public_path($this->user->getRawOriginal('avatar')));
            }
            $validatedData['avatar'] = Helper::fileUpload($request->file('avatar'), 'user/avatar');
        } else {
            $validatedData['avatar'] = $this->user->avatar;
        }

        $this->user->update($validatedData);

        $data = User::select($this->select)->find($this->user->id);

        return Helper::jsonResponse(true, 'Profile updated successfully', 200, $data);
    }
}
