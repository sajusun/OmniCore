<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public array $select;

    public function __construct(
        private UserService $userService,
    ) {
        parent::__construct();
        $this->select = ['id', 'name', 'email', 'avatar', 'last_activity_at'];
    }

    public function me(): JsonResponse
    {
        $user = auth('api')->user();

        if (!$user) {
            return $this->error('Unauthenticated', null, 401);
        }

        return $this->success(new UserResource($user), 'User details fetched successfully');
    }

    public function publicProfile(User $user): JsonResponse
    {
        $data = [
            'user' => new UserResource($user),
        ];

        return $this->success($data, 'Profile data fetched successfully');
    }

    public function onboardingUpdate(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'nullable|string|max:100',
            'avatar'     => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
            'phone'      => 'required|string|numeric|max_digits:20',
            'gender'     => 'required|string|max:255',
            'address'    => 'nullable|string|max:255',
            'country'    => 'nullable|string|max:255',
            'state'      => 'nullable|string|max:255',
            'city'       => 'nullable|string|max:255',
            'zip_code'   => 'nullable|string|max:255',
        ]);

        $user = auth('api')->user();

        if ($request->hasFile('avatar')) {
            if (!empty($user->avatar)) {
                Helper::fileDelete(public_path($user->getRawOriginal('avatar')));
            }
            $validatedData['avatar'] = Helper::fileUpload($request->file('avatar'), 'user/avatar');
        } else {
            $validatedData['avatar'] = $user->avatar;
        }

        $user->update([
            'name'   => trim(($validatedData['first_name'] ?? '') . ' ' . ($validatedData['last_name'] ?? '')),
            'avatar' => $validatedData['avatar'],
        ]);

        $profileData = [
            'first_name' => $validatedData['first_name'],
            'last_name'  => $validatedData['last_name'] ?? null,
            'phone'      => $validatedData['phone'],
            'gender'     => $validatedData['gender'],
            'address'    => $validatedData['address'] ?? null,
            'country'    => $validatedData['country'] ?? null,
            'state'      => $validatedData['state'] ?? null,
            'city'       => $validatedData['city'] ?? null,
            'zip_code'   => $validatedData['zip_code'] ?? null,
        ];

        $user->profile()->updateOrCreate([], $profileData);

        return $this->success(new UserResource($user->fresh('profile')), 'Saved successfully');
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'first_name'   => 'nullable|string|max:100',
            'last_name'    => 'nullable|string|max:100',
            'name'         => 'nullable|string|max:100',
            'avatar'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'cover_photo'  => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'cover_image'  => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'phone'        => 'nullable|string|numeric|max_digits:20',
            'gender'       => 'nullable|string|max:50',
            'password'     => 'nullable|string|min:6|confirmed',
            'address'      => 'nullable|string|max:255',
            'country'      => 'nullable|string|max:255',
            'state'        => 'nullable|string|max:255',
            'city'         => 'nullable|string|max:255',
            'zip_code'     => 'nullable|string|max:255',
            'latitude'     => 'nullable|string|max:255',
            'longitude'    => 'nullable|string|max:255',
            'bio'          => 'nullable|string',
        ]);

        if (!empty($validatedData['password'])) {
            $validatedData['password'] = bcrypt($validatedData['password']);
        } elseif (array_key_exists('password', $validatedData)) {
            unset($validatedData['password']);
        }

        $user = auth('api')->user();

        // Automatically update name if first_name / last_name provided
        if (!empty($validatedData['first_name']) || !empty($validatedData['last_name'])) {
            $firstName = $validatedData['first_name'] ?? ($user->profile?->first_name ?? '');
            $lastName = $validatedData['last_name'] ?? ($user->profile?->last_name ?? '');
            $validatedData['name'] = trim($firstName . ' ' . $lastName);
        }

        if ($request->hasFile('avatar')) {
            if (!empty($user->avatar)) {
                Helper::fileDelete(public_path($user->getRawOriginal('avatar')));
            }
            $validatedData['avatar'] = Helper::fileUpload($request->file('avatar'), 'user/avatar');
        }

        $coverPhotoFile = $request->file('cover_photo') ?? $request->file('cover_image');
        if ($coverPhotoFile) {
            if (!empty($user->profile?->cover_photo)) {
                Helper::fileDelete(public_path($user->profile->getRawOriginal('cover_photo')));
            }
            $validatedData['cover_photo'] = Helper::fileUpload($coverPhotoFile, 'user/cover_photo');
        }

        // Update User model fields
        $userData = collect($validatedData)->only(['name', 'avatar', 'password'])->toArray();
        if (!empty($userData)) {
            $user->update($userData);
        }

        // Update Profile model fields
        $profileFields = [
            'first_name',
            'last_name',
            'phone',
            'gender',
            'address',
            'country',
            'state',
            'city',
            'zip_code',
            'bio',
            'latitude',
            'longitude',
            'cover_photo',
        ];
        $profileData = collect($validatedData)->only($profileFields)->toArray();
        if (!empty($profileData)) {
            $user->profile()->updateOrCreate([], $profileData);
        }

        $user->load('profile');

        return $this->success(new UserResource($user), 'Profile updated successfully');
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required_without:old_password|nullable|string',
            'old_password'     => 'required_without:current_password|nullable|string',
            'password'         => 'required|string|min:6|confirmed',
        ]);

        $currentPassword = $request->input('current_password') ?? $request->input('old_password');
        $user = auth('api')->user();

        if (!Hash::check($currentPassword, $user->password)) {
            return $this->error('Current password does not match.', null, 400);
        }

        $this->userService->updatePassword($user, $request->input('password'));

        return $this->success(null, 'Password updated successfully');
    }

    public function updateAvatar(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);
        $user = auth('api')->user();

        if (!empty($user->avatar)) {
            Helper::fileDelete(public_path($user->avatar));
        }
        $validatedData['avatar'] = Helper::fileUpload($request->file('avatar'), 'user/avatar');
        $user->update($validatedData);

        return $this->success(new UserResource($user), 'Avatar updated successfully');
    }

    public function delete(): JsonResponse
    {
        $user = auth('api')->user();
        if (!empty($user->avatar) && file_exists(public_path($user->avatar))) {
            Helper::fileDelete(public_path($user->avatar));
        }
        auth('api')->logout();
        $user->delete();

        return $this->success(null, 'Profile deleted successfully');
    }

    public function destroy(): JsonResponse
    {
        $user = auth('api')->user();
        if (!empty($user->avatar) && file_exists(public_path($user->avatar))) {
            Helper::fileDelete(public_path($user->avatar));
        }
        auth('api')->logout();
        $user->forceDelete();

        return $this->success(null, 'Profile deleted successfully');
    }
}
