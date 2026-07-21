<?php

namespace App\Services;

use App\Models\User;
use App\Models\FriendRequest;
use App\Services\BaseService;
use App\Enums\FriendRequestStatus;
use Illuminate\Validation\ValidationException;

class FriendService extends BaseService
{
    public function sendRequest(User $sender, User $receiver): FriendRequest
    {
        if ($sender->id === $receiver->id) {
            throw ValidationException::withMessages([
                'user' => 'You cannot send a friend request to yourself.',
            ]);
        }

        if ($sender->isFriend($receiver)) {
            throw ValidationException::withMessages([
                'user' => 'You are already friends.',
            ]);
        }

        if (
            FriendRequest::where(function ($query) use ($sender, $receiver) {
                $query->where('sender_id', $sender->id)->where('receiver_id', $receiver->id);
            })->orWhere(function ($query) use ($sender, $receiver) {
                $query->where('sender_id', $receiver->id)
                    ->where('receiver_id', $sender->id);
            })->where('status', FriendRequestStatus::Pending)
            ->exists()
        ) {
            throw ValidationException::withMessages([
                'user' => 'Friend request already exists.',
            ]);
        }

        return FriendRequest::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'status' => FriendRequestStatus::Pending,
        ]);
    }


    public function accept(User $receiver, FriendRequest $request): void
    {
        if ($request->receiver_id !== $receiver->id) {
            abort(403);
        }

        $request->update([
            'status' => FriendRequestStatus::Accepted,
            'accepted_at' => now(),
        ]);

        $receiver->addFriend($request->sender_id);
    }


    public function reject(User $receiver, FriendRequest $request): void
    {
        if ($request->receiver_id !== $receiver->id) {
            abort(403);
        }

        $request->update([
            'status' => FriendRequestStatus::Rejected,
        ]);
    }


    public function cancel(User $sender, FriendRequest $request): void
    {
        if ($request->sender_id !== $sender->id) {
            abort(403);
        }

        $request->delete();
    }

    public function unfriend(User $user, User $friend): void
    {
        $user->removeFriend($friend);
    }

    public function friends(User $user)
    {
        return $this->applyPagination($user->friends());
    }

    public function pendingRequests(User $user)
    {
        return $this->applyPagination($user->pendingFriendRequests()->with('sender')->latest());
    }


    public function sentRequests(User $user)
    {
        return $this->applyPagination($user->pendingSentFriendRequests()->with('receiver')->latest());
    }
}
