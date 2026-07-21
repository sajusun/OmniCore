<?php

namespace App\Traits;

use App\Models\User;
use App\Models\FriendRequest;
use App\Enums\FriendRequestStatus;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasFriendRequests
{
    /**
     * Requests I sent.
     */
    public function sentFriendRequests(): HasMany
    {
        return $this->hasMany(FriendRequest::class, 'sender_id');
    }

    /**
     * Requests I received.
     */
    public function receivedFriendRequests(): HasMany
    {
        return $this->hasMany(FriendRequest::class, 'receiver_id');
    }

    /**
     * Check if I already sent a request.
     */
    public function hasSentFriendRequest(User|int $user): bool
    {
        $userId = $user instanceof User ? $user->id : $user;

        return $this->sentFriendRequests()
            ->where('receiver_id', $userId)
            ->where('status', FriendRequestStatus::Pending)
            ->exists();
    }

    /**
     * Check if I received a request.
     */
    public function hasReceivedFriendRequest(User|int $user): bool
    {
        $userId = $user instanceof User ? $user->id : $user;

        return $this->receivedFriendRequests()
            ->where('sender_id', $userId)
            ->where('status', FriendRequestStatus::Pending)
            ->exists();
    }

    /**
     * Pending requests I received.
     */
    public function pendingFriendRequests()
    {
        return $this->receivedFriendRequests()
            ->where('status', FriendRequestStatus::Pending);
    }

    /**
     * Pending requests I sent.
     */
    public function pendingSentFriendRequests()
    {
        return $this->sentFriendRequests()
            ->where('status', FriendRequestStatus::Pending);
    }
}