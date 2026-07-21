<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\FriendRequest;
use App\Services\FriendService;
use App\Http\Controllers\Controller;
use App\Http\Resources\FriendResource;

class FriendController extends Controller
{
    private User $user;

    public function __construct(private readonly FriendService $friendService)
    {
        $this->user = auth('api')->user();
    }

    public function sendRequest(User $user)
    {
        try {
            $this->friendService->sendRequest($this->user, $user);

            return $this->success(message: 'Friend request sent successfully.');
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }


    public function accept(FriendRequest $friendRequest)
    {
        try {
            $this->friendService->accept($this->user, $friendRequest);

            return $this->success(message: 'Friend request accepted successfully.');
        } catch (\Throwable $e) {
            return $this->error(message: $e->getMessage(), status: 400);
        }
    }


    public function reject(FriendRequest $friendRequest)
    {
        try {
            $this->friendService->reject($this->user, $friendRequest);

            return $this->success(message: 'Friend request rejected successfully.');
        } catch (\Throwable $e) {
            return $this->error(message: $e->getMessage(), status: 400);
        }
    }


    public function cancel(FriendRequest $friendRequest)
    {
        try {
            $this->friendService->cancel($this->user, $friendRequest);

            return $this->success(message: 'Friend request cancelled successfully.');
        } catch (\Throwable $e) {
            return $this->error(message: $e->getMessage(), status: 400);
        }
    }


    public function unfriend(User $user)
    {
        try {
            $this->friendService->unfriend($this->user, $user);

            return $this->success(message: 'User unfriended successfully.');
        } catch (\Throwable $e) {
            return $this->error(message: $e->getMessage(), status: 400);
        }
    }


    public function friends()
    {
        try {
            $friends = $this->friendService->friends($this->user);

            return $this->response(message: 'Friends fetched successfully.', data: FriendResource::collection($friends), paginate: true, paginateData: $friends);
        } catch (\Throwable $e) {
            return $this->error(message: $e->getMessage(), status: 400);
        }
    }


    public function pendingRequests(Request $request)
    {
        try {
            $requests = $this->friendService->pendingRequests($this->user);

            return $this->response(
                message: 'Pending requests fetched successfully.',
                data: FriendResource::collection($requests),
                paginate: true,
                paginateData: $requests
            );
        } catch (\Throwable $e) {
            return $this->error(message: $e->getMessage(), status: 400);
        }
    }

    /**
     * Sent Requests
     */
    public function sentRequests()
    {
        try {
            $requests = $this->friendService->sentRequests($this->user);
            return $this->response(message: 'Sent requests fetched successfully.', data: FriendResource::collection($requests), paginate: true, paginateData: $requests);
        } catch (\Throwable $e) {
            return $this->error(message: $e->getMessage(), status: 400);
        }
    }
}
