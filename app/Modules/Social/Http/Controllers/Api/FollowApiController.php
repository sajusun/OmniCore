<?php

namespace App\Modules\Social\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Social\Http\Resources\FollowerResource;
use App\Modules\Social\Services\FollowService;
use Illuminate\Http\JsonResponse;

class FollowApiController extends Controller
{
    public function __construct(private readonly FollowService $followService) {}

    public function follow(User $user): JsonResponse
    {
        $authUser = auth('api')->user() ?? auth()->user();
        $this->followService->follow($authUser, $user);

        return Helper::jsonResponse(true, 'User followed successfully.', 200);
    }

    public function unfollow(User $user): JsonResponse
    {
        $authUser = auth('api')->user() ?? auth()->user();
        $this->followService->unfollow($authUser, $user);

        return Helper::jsonResponse(true, 'User unfollowed successfully.', 200);
    }

    public function toggle(User $user): JsonResponse
    {
        $authUser = auth('api')->user() ?? auth()->user();
        $following = $this->followService->toggle($authUser, $user);

        return Helper::jsonResponse(
            true,
            $following ? 'User followed successfully.' : 'User unfollowed successfully.',
            200,
            ['is_following' => $following]
        );
    }

    public function followers(): JsonResponse
    {
        $authUser = auth('api')->user() ?? auth()->user();
        $followers = $this->followService->followers($authUser);

        return Helper::jsonResponse(
            true,
            'Followers fetched successfully.',
            200,
            FollowerResource::collection($followers),
            [
                'current_page' => $followers->currentPage(),
                'last_page'    => $followers->lastPage(),
                'total'        => $followers->total(),
            ]
        );
    }

    public function followings(): JsonResponse
    {
        $authUser = auth('api')->user() ?? auth()->user();
        $followings = $this->followService->followings($authUser);

        return Helper::jsonResponse(
            true,
            'Followings fetched successfully.',
            200,
            FollowerResource::collection($followings),
            [
                'current_page' => $followings->currentPage(),
                'last_page'    => $followings->lastPage(),
                'total'        => $followings->total(),
            ]
        );
    }

    public function userFollowers(User $user): JsonResponse
    {
        $followers = $this->followService->followers($user);

        return Helper::jsonResponse(
            true,
            'User followers fetched successfully.',
            200,
            FollowerResource::collection($followers),
            [
                'current_page' => $followers->currentPage(),
                'last_page'    => $followers->lastPage(),
                'total'        => $followers->total(),
            ]
        );
    }

    public function userFollowings(User $user): JsonResponse
    {
        $followings = $this->followService->followings($user);

        return Helper::jsonResponse(
            true,
            'User followings fetched successfully.',
            200,
            FollowerResource::collection($followings),
            [
                'current_page' => $followings->currentPage(),
                'last_page'    => $followings->lastPage(),
                'total'        => $followings->total(),
            ]
        );
    }
}
