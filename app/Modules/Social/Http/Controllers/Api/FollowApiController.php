<?php

namespace App\Modules\Social\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Social\Http\Resources\FollowerResource;
use App\Modules\Social\Services\FollowService;
use Illuminate\Http\JsonResponse;

class FollowApiController extends Controller
{
    public function __construct(private readonly FollowService $followService)
    {
        parent::__construct();
    }

    public function follow(User $user): JsonResponse
    {
        $authUser = auth('api')->user() ?? auth()->user();
        $this->followService->follow($authUser, $user);

        return $this->success(null, 'User followed successfully.');
    }

    public function unfollow(User $user): JsonResponse
    {
        $authUser = auth('api')->user() ?? auth()->user();
        $this->followService->unfollow($authUser, $user);

        return $this->success(null, 'User unfollowed successfully.');
    }

    public function toggle(User $user): JsonResponse
    {
        $authUser = auth('api')->user() ?? auth()->user();
        $following = $this->followService->toggle($authUser, $user);

        return $this->success(
            ['is_following' => $following],
            $following ? 'User followed successfully.' : 'User unfollowed successfully.'
        );
    }

    public function followers(): JsonResponse
    {
        $authUser = auth('api')->user() ?? auth()->user();
        $followers = $this->followService->followers($authUser);

        return $this->paginated(
            $followers,
            FollowerResource::class,
            'Followers fetched successfully.'
        );
    }

    public function followings(): JsonResponse
    {
        $authUser = auth('api')->user() ?? auth()->user();
        $followings = $this->followService->followings($authUser);

        return $this->paginated(
            $followings,
            FollowerResource::class,
            'Followings fetched successfully.'
        );
    }

    public function userFollowers(User $user): JsonResponse
    {
        $followers = $this->followService->followers($user);

        return $this->paginated(
            $followers,
            FollowerResource::class,
            'User followers fetched successfully.'
        );
    }

    public function userFollowings(User $user): JsonResponse
    {
        $followings = $this->followService->followings($user);

        return $this->paginated(
            $followings,
            FollowerResource::class,
            'User followings fetched successfully.'
        );
    }
}
