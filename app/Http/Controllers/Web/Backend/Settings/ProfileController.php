<?php

namespace App\Http\Controllers\Web\Backend\Settings;

use App\Models\User;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('backend.settings.profile_settings', compact('user'));
    }

    public function UpdateProfile(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        // if ($request->hasFile('image')) {
        //     if ($user->avatar && $user->avatar != 'default/profile.jpg') {
        //         Helper::fileDelete(public_path($user->avatar));
        //     }
        //     $path = Helper::fileUpload($request->file('image'), 'users', $user->name);
        //     $user->avatar = $path;
        // }

        $user->update($request->only(['name', 'email']));

        return redirect()->back()->with('success', 'Profile updated successfully');
    }

    public function UpdateProfilePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = auth()->user();

        if ($request->hasFile('profile_picture')) {
            if ($user->avatar && $user->avatar != 'default/profile.jpg') {
                Helper::fileDelete(public_path($user->avatar));
            }
            $path = Helper::fileUpload($request->file('profile_picture'), 'users', $user->name);
            $user->update(['avatar' => $path]);

            return response()->json([
                'success' => true,
                'image_url' => asset($path)
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No image uploaded']);
    }

    public function UpdatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->old_password, $user->password)) {
            return redirect()->back()->with('t-error', 'Current password does not match');
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->back()->with('t-success', 'Password updated successfully');
    }
}
