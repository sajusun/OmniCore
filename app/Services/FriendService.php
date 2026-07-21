<?php

namespace App\Services;

use App\Models\User;
use App\Models\FriendRequest;
use App\Enums\FriendRequestStatus;
use Illuminate\Validation\ValidationException;

class FriendService
{
    /**
     * Send friend request.
     */
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
                $query->where('sender_id', $sender->id)
                    ->where('receiver_id', $receiver->id);
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

    /**
     * Accept friend request.
     */
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

    /**
     * Reject friend request.
     */
    public function reject(User $receiver, FriendRequest $request): void
    {
        if ($request->receiver_id !== $receiver->id) {
            abort(403);
        }

        $request->update([
            'status' => FriendRequestStatus::Rejected,
        ]);
    }

    /**
     * Cancel sent request.
     */
    public function cancel(User $sender, FriendRequest $request): void
    {
        if ($request->sender_id !== $sender->id) {
            abort(403);
        }

        $request->delete();
    }

    /**
     * Unfriend.
     */
    public function unfriend(User $user, User $friend): void
    {
        $user->removeFriend($friend);
    }

    /**
     * Friend list.
     */
    public function friends(User $user)
    {
        return $user->friends()->paginate(20);
    }

    /**
     * Received pending requests.
     */
    public function pendingRequests(User $user)
    {
        return $user->pendingFriendRequests()
            ->with('sender')
            ->latest()
            ->paginate(20);
    }

    /**
     * Sent pending requests.
     */
    public function sentRequests(User $user)
    {
        return $user->pendingSentFriendRequests()
            ->with('receiver')
            ->latest()
            ->paginate(20);
    }
}
