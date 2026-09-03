<?php

namespace App\Modules\Social\Services;

use App\Models\User;
use App\Modules\Social\Enums\FriendRequestStatus;
use App\Modules\Social\Models\Friend;
use App\Modules\Social\Models\FriendRequest;
use App\Services\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
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

        if ($sender->hasBlocked($receiver) || $sender->isBlockedBy($receiver)) {
            throw ValidationException::withMessages([
                'user' => 'Cannot send friend request to this user.',
            ]);
        }

        if ($sender->isFriend($receiver)) {
            throw ValidationException::withMessages([
                'user' => 'You are already friends.',
            ]);
        }

        $existingRequest = FriendRequest::where(function ($query) use ($sender, $receiver) {
            $query->where('sender_id', $sender->id)->where('receiver_id', $receiver->id);
        })->orWhere(function ($query) use ($sender, $receiver) {
            $query->where('sender_id', $receiver->id)->where('receiver_id', $sender->id);
        })->where('status', FriendRequestStatus::Pending)->first();

        if ($existingRequest) {
            throw ValidationException::withMessages([
                'user' => 'A pending friend request already exists between you and this user.',
            ]);
        }

        return FriendRequest::create([
            'sender_id'   => $sender->id,
            'receiver_id' => $receiver->id,
            'status'      => FriendRequestStatus::Pending,
        ])->load(['sender', 'receiver']);
    }

    public function accept(User $receiver, FriendRequest $request): void
    {
        if ($request->receiver_id !== $receiver->id) {
            abort(403, 'Unauthorized to accept this friend request.');
        }

        if ($request->status !== FriendRequestStatus::Pending) {
            throw ValidationException::withMessages([
                'request' => 'This request is no longer pending.',
            ]);
        }

        DB::transaction(function () use ($receiver, $request) {
            $request->update([
                'status'      => FriendRequestStatus::Accepted,
                'accepted_at' => now(),
            ]);

            $receiver->addFriend($request->sender_id);
        });
    }

    public function reject(User $receiver, FriendRequest $request): void
    {
        if ($request->receiver_id !== $receiver->id) {
            abort(403, 'Unauthorized to reject this friend request.');
        }

        $request->update([
            'status' => FriendRequestStatus::Rejected,
        ]);
    }

    public function cancel(User $sender, FriendRequest $request): void
    {
        if ($request->sender_id !== $sender->id) {
            abort(403, 'Unauthorized to cancel this friend request.');
        }

        $request->delete();
    }

    public function unfriend(User $user, User $friend): void
    {
        $user->removeFriend($friend);
    }

    public function friends(User $user, ?string $search = null)
    {
        $query = $user->friends()->when($search, function ($q, $search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        })->latest('friends.created_at');

        return $this->applyPagination($query);
    }

    public function pendingRequests(User $user)
    {
        return $this->applyPagination(
            $user->pendingFriendRequests()->with('sender')->latest()
        );
    }

    public function sentRequests(User $user)
    {
        return $this->applyPagination(
            $user->pendingSentFriendRequests()->with('receiver')->latest()
        );
    }

    public function mutualFriends(User $user, User $target)
    {
        return $this->applyPagination(
            $user->mutualFriendsQuery($target)->latest()
        );
    }

    /**
     * Smart Friendship Suggestions based on mutual friends & non-connections
     */
    public function suggestions(User $user, int $limit = 15)
    {
        $blockedIds = $user->blockedUsers()->pluck('users.id')
            ->merge($user->blockedByUsers()->pluck('users.id'))
            ->toArray();

        $friendIds = $user->friends()->pluck('users.id')->toArray();
        $pendingSentIds = $user->pendingSentFriendRequests()->pluck('receiver_id')->toArray();
        $pendingReceivedIds = $user->pendingFriendRequests()->pluck('sender_id')->toArray();

        $excludedIds = array_unique(array_merge(
            [$user->id],
            $blockedIds,
            $friendIds,
            $pendingSentIds,
            $pendingReceivedIds
        ));

        // Rank suggestions by highest count of mutual friends
        return User::query()
            ->select('users.*')
            ->selectRaw('(
                SELECT COUNT(*)
                FROM friends AS f1
                INNER JOIN friends AS f2 ON f1.friend_id = f2.friend_id
                WHERE f1.user_id = ? AND f2.user_id = users.id
            ) as mutual_count', [$user->id])
            ->whereNotIn('users.id', $excludedIds)
            ->orderByDesc('mutual_count')
            ->latest('users.created_at')
            ->paginate($limit);
    }
}
