<?php

namespace App\Modules\Social\Http\Controllers\Api;

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
    public function __construct(private readonly FriendService $friendService)
    {
        parent::__construct();
    }

    public function friends(Request $request): JsonResponse
    {
        $user = auth('api')->user() ?? auth()->user();
        $friends = $this->friendService->friends($user, $request->query('search'));

        return $this->paginated(
            $friends,
            FriendResource::class,
            'Friends fetched successfully.'
        );
    }

    public function sendRequest(User $user): JsonResponse
    {
        $authUser = auth('api')->user() ?? auth()->user();
        $friendRequest = $this->friendService->sendRequest($authUser, $user);

        return $this->created(
            new FriendRequestResource($friendRequest),
            'Friend request sent successfully.'
        );
    }

    public function accept(FriendRequest $friendRequest): JsonResponse
    {
        $authUser = auth('api')->user() ?? auth()->user();
        $this->friendService->accept($authUser, $friendRequest);

        return $this->success(null, 'Friend request accepted successfully.');
    }

    public function reject(FriendRequest $friendRequest): JsonResponse
    {
        $authUser = auth('api')->user() ?? auth()->user();
        $this->friendService->reject($authUser, $friendRequest);

        return $this->success(null, 'Friend request rejected successfully.');
    }

    public function cancel(FriendRequest $friendRequest): JsonResponse
    {
        $authUser = auth('api')->user() ?? auth()->user();
        $this->friendService->cancel($authUser, $friendRequest);

        return $this->success(null, 'Friend request cancelled successfully.');
    }

    public function unfriend(User $user): JsonResponse
    {
        $authUser = auth('api')->user() ?? auth()->user();
        $this->friendService->unfriend($authUser, $user);

        return $this->success(null, 'User unfriended successfully.');
    }

    public function pendingRequests(): JsonResponse
    {
        $authUser = auth('api')->user() ?? auth()->user();
        $requests = $this->friendService->pendingRequests($authUser);

        return $this->paginated(
            $requests,
            FriendRequestResource::class,
            'Pending friend requests fetched successfully.'
        );
    }

    public function sentRequests(): JsonResponse
    {
        $authUser = auth('api')->user() ?? auth()->user();
        $requests = $this->friendService->sentRequests($authUser);

        return $this->paginated(
            $requests,
            FriendRequestResource::class,
            'Sent friend requests fetched successfully.'
        );
    }

    public function mutualFriends(User $user): JsonResponse
    {
        $authUser = auth('api')->user() ?? auth()->user();
        $mutuals = $this->friendService->mutualFriends($authUser, $user);

        return $this->paginated(
            $mutuals,
            FriendResource::class,
            'Mutual friends fetched successfully.'
        );
    }

    public function suggestions(Request $request): JsonResponse
    {
        $authUser = auth('api')->user() ?? auth()->user();
        $limit = (int) ($request->query('limit', 15));
        $suggestions = $this->friendService->suggestions($authUser, $limit);

        return $this->paginated(
            $suggestions,
            FriendResource::class,
            'Friend suggestions fetched successfully.'
        );
    }
}
