<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Services\ClubService;
use App\Services\UserService;
use App\Services\EventService;
use App\Services\VehicleService;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClubResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\EventResource;
use App\Http\Resources\VehicleResource;

class UserController extends Controller
{

    public array $select;

    public User $user;

    public function __construct(
        private UserService $userService,
        private EventService $eventService,
        private VehicleService $vehicleService,
        private ClubService $clubService,
    ) {
        parent::__construct();
        $this->user = auth('api')->user();
        $this->select = ['id', 'name', 'email', 'avatar', 'last_activity_at'];
    }

    public function me()
    {

        $vehicle = $this->user->vehicles()->count();
        $post = $this->user->posts()->count();
        $club = $this->user->clubs()->count();
        $event = $this->user->events()->count();
        $data = [
            'vehicles' => $vehicle,
            'posts' => $post,
            'clubs' => $club,
            'events' => $event
        ];
        $this->user->info = $data;
        return $this->success(
            message: 'User details fetched successfully',
            status: 200,
            data: new UserResource($this->user)
        );
    }

    public function publicProfile(User $user)
    {

        $events = $user->events()->where('status', 'published')->where('is_public', 1)->get();
        $vehicles = $user->vehicles()->where('status', 'public')->get();
        $clubs = $user->clubs()->where('status', 'published')->get();
        $posts = $user->posts()->where('status', 'published')->where('visibility', 'public')->get();
        $data = [
            'user' => new UserResource($user),
            'events' => EventResource::collection($events),
            'vehicles' => VehicleResource::collection($vehicles),
            'clubs' => ClubResource::collection($clubs),
            'posts'=> PostResource::collection($posts),
            'event_count' => $events->count(),
            'vehicle_count' => $vehicles->count(),
            'club_count' => $clubs->count(),
            'post_count'=> $posts->count(),
        ];

        return $this->success(data: $data, message: "profile data fetch success", status: 200);
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
            'name' => $validatedData['first_name'] . ' ' . $validatedData['last_name'] ?? $validatedData['name'],
            'avatar' => $validatedData['avatar'],
        ]);

        $data = [
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'] ?? null,
            'phone' => $validatedData['phone'],
            'gender' => $validatedData['gender'],
            'address' => $validatedData['address'] ?? null,
            'country' => $validatedData['country'] ?? null,
            'state' => $validatedData['state'] ?? null,
            'city' => $validatedData['city'] ?? null,
            'zip_code' => $validatedData['zip_code'] ?? null,
        ];

        $user->profile()->updateOrCreate([], $data);

        return $this->success(message: 'Saved successfully', status: 200, data: new UserResource($user));
    }

    public function updateProfile(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'name' => 'nullable|string|max:100',
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
            'bio' => 'nullable|string',

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
        $data = [
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
        ];
        $data = collect($validatedData)
            ->only($data)->toArray();

        $user->update($validatedData);
        $user->profile()->updateOrCreate([], $data);

        return $this->success(message: 'Profile updated successfully', status: 200, data: new UserResource($user));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required_without:old_password|nullable|string',
            'old_password' => 'required_without:current_password|nullable|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $currentPassword = $request->input('current_password') ?? $request->input('old_password');
        $user = auth('api')->user() ?? $this->user;

        if (! Hash::check($currentPassword, $user->password)) {
            return $this->error(message: 'Current password does not match.', status: 400);
        }

        $this->userService->updatePassword($user, $request->input('password'));

        return $this->success(message: 'Password updated successfully', status: 200);
    }

    public function updateAvatar(Request $request)
    {
        $validatedData = $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);
        $user = $this->user;

        if (! empty($user->avatar)) {
            Helper::fileDelete(public_path($user->avatar));
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
