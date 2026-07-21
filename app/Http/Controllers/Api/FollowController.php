<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FollowerResource;
use App\Models\User;
use App\Services\FollowService;
use Illuminate\Http\JsonResponse;

class FollowController extends Controller
{
    private User $user;

    public function __construct(private readonly FollowService $followService)
    {
        $this->user = auth('api')->user();
    }

    public function follow(User $user): JsonResponse
    {
        $this->followService->follow($this->user, $user);

        return $this->success(message: 'User followed successfully.');
    }

    public function unfollow(User $user): JsonResponse
    {
        $this->followService->unfollow($this->user, $user);

        return $this->success(message: 'User unfollowed successfully.');
    }

    public function toggle(User $user): JsonResponse
    {
        $following = $this->followService->toggle($this->user, $user);

        return $this->success(
            message: $following ? 'User followed successfully.' : 'User unfollowed successfully.',
            data: [
                'follow' => $following,
            ],
        );
    }

    public function followers(): JsonResponse
    {
        $data = $this->followService->followers($this->user);

        return $this->response(
            message: 'Followers',
            data: FollowerResource::collection($data),
            paginate: true,
            paginateData: $data
        );
    }

    public function followings(): JsonResponse
    {
        $data = $this->followService->followings($this->user);

        return $this->response(
            message: 'Following',
            data: FollowerResource::collection($data),
            paginate: true,
            paginateData: $data
        );
    }
}
