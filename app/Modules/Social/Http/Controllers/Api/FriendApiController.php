<?php

namespace App\Modules\Social\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Social\Http\Resources\FriendRequestResource;
use App\Modules\Social\Http\Resources\FriendResource;
use App\Modules\Social\Models\FriendRequest;
use App\Modules\Social\Services\FriendService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FriendApiController extends Controller
{
    public function __construct(private readonly FriendService $friendService) {}

    public function friends(Request $request): JsonResponse
    {
        $user = auth('api')->user() ?? auth()->user();
        $friends = $this->friendService->friends($user, $request->query('search'));

        return Helper::jsonResponse(
            true,
            'Friends fetched successfully.',
            200,
            FriendResource::collection($friends),
            [
                'current_page' => $friends->currentPage(),
                'last_page'    => $friends->lastPage(),
                'total'        => $friends->total(),
            ]
        );
    }

    public function sendRequest(User $user): JsonResponse
    {
        $authUser = auth('api')->user() ?? auth()->user();
        $request = $this->friendService->sendRequest($authUser, $user);

        return Helper::jsonResponse(
            true,
            'Friend request sent successfully.',
            201,
            new FriendRequestResource($request)
        );
    }

    public function accept(FriendRequest $friendRequest): JsonResponse
    {
        $authUser = auth('api')->user() ?? auth()->user();
        $this->friendService->accept($authUser, $friendRequest);

        return Helper::jsonResponse(true, 'Friend request accepted successfully.', 200);
    }

    public function reject(FriendRequest $friendRequest): JsonResponse
    {
        $authUser = auth('api')->user() ?? auth()->user();
        $this->friendService->reject($authUser, $friendRequest);

        return Helper::jsonResponse(true, 'Friend request rejected successfully.', 200);
    }

    public function cancel(FriendRequest $friendRequest): JsonResponse
    {
        $authUser = auth('api')->user() ?? auth()->user();
        $this->friendService->cancel($authUser, $friendRequest);

        return Helper::jsonResponse(true, 'Friend request cancelled successfully.', 200);
    }

    public function unfriend(User $user): JsonResponse
    {
        $authUser = auth('api')->user() ?? auth()->user();
        $this->friendService->unfriend($authUser, $user);

        return Helper::jsonResponse(true, 'User unfriended successfully.', 200);
    }

    public function pendingRequests(): JsonResponse
    {
        $authUser = auth('api')->user() ?? auth()->user();
        $requests = $this->friendService->pendingRequests($authUser);

        return Helper::jsonResponse(
            true,
            'Pending friend requests fetched successfully.',
            200,
            FriendRequestResource::collection($requests),
            [
                'current_page' => $requests->currentPage(),
                'last_page'    => $requests->lastPage(),
                'total'        => $requests->total(),
            ]
        );
    }

    public function sentRequests(): JsonResponse
    {
        $authUser = auth('api')->user() ?? auth()->user();
        $requests = $this->friendService->sentRequests($authUser);

        return Helper::jsonResponse(
            true,
            'Sent friend requests fetched successfully.',
            200,
            FriendRequestResource::collection($requests),
            [
                'current_page' => $requests->currentPage(),
                'last_page'    => $requests->lastPage(),
                'total'        => $requests->total(),
            ]
        );
    }

    public function mutualFriends(User $user): JsonResponse
    {
        $authUser = auth('api')->user() ?? auth()->user();
        $mutuals = $this->friendService->mutualFriends($authUser, $user);

        return Helper::jsonResponse(
            true,
            'Mutual friends fetched successfully.',
            200,
            FriendResource::collection($mutuals),
            [
                'current_page' => $mutuals->currentPage(),
                'last_page'    => $mutuals->lastPage(),
                'total'        => $mutuals->total(),
            ]
        );
    }

    public function suggestions(Request $request): JsonResponse
    {
        $authUser = auth('api')->user() ?? auth()->user();
        $limit = (int) ($request->query('limit', 15));
        $suggestions = $this->friendService->suggestions($authUser, $limit);

        return Helper::jsonResponse(
            true,
            'Friend suggestions fetched successfully.',
            200,
            FriendResource::collection($suggestions),
            [
                'current_page' => $suggestions->currentPage(),
                'last_page'    => $suggestions->lastPage(),
                'total'        => $suggestions->total(),
            ]
        );
    }
}
