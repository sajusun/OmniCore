<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\FollowService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

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

        return response()->json([
            'message' => 'User followed successfully.',
        ]);
    }

    public function unfollow(User $user): JsonResponse
    {
        $this->followService->unfollow($this->user, $user);

        return response()->json([
            'message' => 'User unfollowed successfully.',
        ]);
    }

    public function toggle(User $user): JsonResponse
    {
        $following = $this->followService->toggle($this->user, $user);

        return response()->json([
            'message' => $following
                ? 'User followed successfully.'
                : 'User unfollowed successfully.',
            'following' => $following,
        ]);
    }

    public function followers(): JsonResponse
    {
        return response()->json(
            $this->followService->followers($this->user)
        );
    }

    public function followings(): JsonResponse
    {
        return response()->json(
            $this->followService->followings($this->user)
        );
    }
}
