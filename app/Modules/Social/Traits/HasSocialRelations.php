<?php

namespace App\Modules\Social\Traits;

use App\Models\User;
use App\Modules\Social\Enums\FriendRequestStatus;
use App\Modules\Social\Enums\RelationshipType;
use App\Modules\Social\Models\Follower;
use App\Modules\Social\Models\Friend;
use App\Modules\Social\Models\FriendRequest;
use App\Modules\Social\Models\UserBlock;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

trait HasSocialRelations
{
    /*
    |--------------------------------------------------------------------------
    | Friendship Relationships & Helpers
    |--------------------------------------------------------------------------
    */

    public function friends(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'friends',
            'user_id',
            'friend_id'
        )->withTimestamps();
    }

    public function addFriend(User|int $friend): void
    {
        $friendId = $friend instanceof User ? $friend->id : $friend;

        if ($friendId === $this->id) {
            return;
        }

        Friend::firstOrCreate([
            'user_id' => $this->id,
            'friend_id' => $friendId,
        ]);

        Friend::firstOrCreate([
            'user_id' => $friendId,
            'friend_id' => $this->id,
        ]);
    }

    public function removeFriend(User|int $friend): void
    {
        $friendId = $friend instanceof User ? $friend->id : $friend;

        Friend::where([
            'user_id' => $this->id,
            'friend_id' => $friendId,
        ])->delete();

        Friend::where([
            'user_id' => $friendId,
            'friend_id' => $this->id,
        ])->delete();
    }

    public function isFriend(User|int $friend): bool
    {
        $friendId = $friend instanceof User ? $friend->id : $friend;

        return Friend::where([
            'user_id' => $this->id,
            'friend_id' => $friendId,
        ])->exists();
    }

    public function friendsCount(): int
    {
        return $this->friends()->count();
    }

    /*
    |--------------------------------------------------------------------------
    | Friend Request Relationships & Helpers
    |--------------------------------------------------------------------------
    */

    public function sentFriendRequests(): HasMany
    {
        return $this->hasMany(FriendRequest::class, 'sender_id');
    }

    public function receivedFriendRequests(): HasMany
    {
        return $this->hasMany(FriendRequest::class, 'receiver_id');
    }

    public function pendingFriendRequests(): HasMany
    {
        return $this->receivedFriendRequests()
            ->where('status', FriendRequestStatus::Pending);
    }

    public function pendingSentFriendRequests(): HasMany
    {
        return $this->sentFriendRequests()
            ->where('status', FriendRequestStatus::Pending);
    }

    public function hasSentFriendRequest(User|int $user): bool
    {
        $userId = $user instanceof User ? $user->id : $user;

        return $this->sentFriendRequests()
            ->where('receiver_id', $userId)
            ->where('status', FriendRequestStatus::Pending)
            ->exists();
    }

    public function hasReceivedFriendRequest(User|int $user): bool
    {
        $userId = $user instanceof User ? $user->id : $user;

        return $this->receivedFriendRequests()
            ->where('sender_id', $userId)
            ->where('status', FriendRequestStatus::Pending)
            ->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | Follower Network Relationships & Helpers
    |--------------------------------------------------------------------------
    */

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'followers',
            'user_id',
            'follower_id'
        )->withTimestamps();
    }

    public function followings(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'followers',
            'follower_id',
            'user_id'
        )->withTimestamps();
    }

    public function follow(User|int $user): void
    {
        $userId = $user instanceof User ? $user->id : $user;

        if ($userId == $this->id) {
            return;
        }

        $this->followings()->syncWithoutDetaching([$userId]);
    }

    public function unfollow(User|int $user): void
    {
        $userId = $user instanceof User ? $user->id : $user;

        $this->followings()->detach($userId);
    }

    public function isFollowing(User|int $user): bool
    {
        $userId = $user instanceof User ? $user->id : $user;

        return $this->followings()
            ->where('user_id', $userId)
            ->exists();
    }

    public function isFollowedBy(User|int $user): bool
    {
        $userId = $user instanceof User ? $user->id : $user;

        return $this->followers()
            ->where('follower_id', $userId)
            ->exists();
    }

    public function followersCount(): int
    {
        return $this->followers()->count();
    }

    public function followingsCount(): int
    {
        return $this->followings()->count();
    }

    /*
    |--------------------------------------------------------------------------
    | Block / Privacy Relationships & Helpers
    |--------------------------------------------------------------------------
    */

    public function blockedUsers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'user_blocks',
            'user_id',
            'blocked_user_id'
        )->withTimestamps();
    }

    public function blockedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'user_blocks',
            'blocked_user_id',
            'user_id'
        )->withTimestamps();
    }

    public function block(User|int $user): void
    {
        $userId = $user instanceof User ? $user->id : $user;

        if ($userId == $this->id) {
            return;
        }

        // Add to blocks
        $this->blockedUsers()->syncWithoutDetaching([$userId]);

        // Break mutual friendship
        $this->removeFriend($userId);

        // Break mutual followings
        $this->unfollow($userId);
        if ($user instanceof User) {
            $user->unfollow($this->id);
        } else {
            Follower::where('user_id', $this->id)->where('follower_id', $userId)->delete();
        }

        // Remove pending friend requests
        FriendRequest::where(function ($q) use ($userId) {
            $q->where('sender_id', $this->id)->where('receiver_id', $userId);
        })->orWhere(function ($q) use ($userId) {
            $q->where('sender_id', $userId)->where('receiver_id', $this->id);
        })->delete();
    }

    public function unblock(User|int $user): void
    {
        $userId = $user instanceof User ? $user->id : $user;

        $this->blockedUsers()->detach($userId);
    }

    public function hasBlocked(User|int $user): bool
    {
        $userId = $user instanceof User ? $user->id : $user;

        return $this->blockedUsers()->where('blocked_user_id', $userId)->exists();
    }

    public function isBlockedBy(User|int $user): bool
    {
        $userId = $user instanceof User ? $user->id : $user;

        return $this->blockedByUsers()->where('user_id', $userId)->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | Mutual Friends & Social Graph Insights
    |--------------------------------------------------------------------------
    */

    /**
     * Get Mutual Friends Query Builder between this user and target user
     */
    public function mutualFriendsQuery(User|int $targetUser): Builder
    {
        $targetId = $targetUser instanceof User ? $targetUser->id : $targetUser;

        return User::query()
            ->whereHas('friends', fn ($q) => $q->where('friends.user_id', $this->id))
            ->whereHas('friends', fn ($q) => $q->where('friends.user_id', $targetId))
            ->whereNotIn('id', [$this->id, $targetId]);
    }

    /**
     * Get Mutual Friends Collection
     */
    public function getMutualFriends(User|int $targetUser, int $limit = 10): Collection
    {
        return $this->mutualFriendsQuery($targetUser)->limit($limit)->get();
    }

    /**
     * Get Mutual Friends Count
     */
    public function getMutualFriendsCount(User|int $targetUser): int
    {
        $targetId = $targetUser instanceof User ? $targetUser->id : $targetUser;

        return DB::table('friends as f1')
            ->join('friends as f2', 'f1.friend_id', '=', 'f2.friend_id')
            ->where('f1.user_id', $this->id)
            ->where('f2.user_id', $targetId)
            ->whereNotIn('f1.friend_id', [$this->id, $targetId])
            ->count();
    }

    /**
     * Get Detailed Social Relationship Overview with a target user
     */
    public function getRelationshipWith(User|int $targetUser): array
    {
        $target = $targetUser instanceof User ? $targetUser : User::find($targetUser);

        if (!$target) {
            return ['type' => RelationshipType::NONE->value];
        }

        if ($this->id === $target->id) {
            return [
                'type'                 => RelationshipType::SELF->value,
                'is_self'              => true,
                'is_friend'            => false,
                'is_following'         => false,
                'is_followed_by'       => false,
                'has_blocked'          => false,
                'is_blocked_by'        => false,
                'friend_request'       => null,
                'mutual_friends_count' => 0,
            ];
        }

        $hasBlocked    = $this->hasBlocked($target);
        $isBlockedBy   = $this->isBlockedBy($target);
        $isFriend      = $this->isFriend($target);
        $isFollowing   = $this->isFollowing($target);
        $isFollowedBy  = $this->isFollowedBy($target);
        $mutualCount   = $this->getMutualFriendsCount($target);

        $pendingSent     = $this->pendingSentFriendRequests()->where('receiver_id', $target->id)->first();
        $pendingReceived = $this->pendingFriendRequests()->where('sender_id', $target->id)->first();

        $friendRequestData = null;
        if ($pendingSent) {
            $friendRequestData = [
                'direction'  => 'sent',
                'request_id' => $pendingSent->id,
                'created_at' => $pendingSent->created_at,
            ];
        } elseif ($pendingReceived) {
            $friendRequestData = [
                'direction'  => 'received',
                'request_id' => $pendingReceived->id,
                'created_at' => $pendingReceived->created_at,
            ];
        }

        // Determine primary relationship type
        $type = RelationshipType::NONE;
        if ($hasBlocked) {
            $type = RelationshipType::BLOCKED;
        } elseif ($isBlockedBy) {
            $type = RelationshipType::BLOCKED_BY;
        } elseif ($isFriend) {
            $type = RelationshipType::FRIEND;
        } elseif ($pendingSent) {
            $type = RelationshipType::REQUEST_SENT;
        } elseif ($pendingReceived) {
            $type = RelationshipType::REQUEST_RECEIVED;
        } elseif ($isFollowing && $isFollowedBy) {
            $type = RelationshipType::MUTUAL_FOLLOW;
        } elseif ($isFollowing) {
            $type = RelationshipType::FOLLOWING_ONLY;
        } elseif ($isFollowedBy) {
            $type = RelationshipType::FOLLOWED_BY_ONLY;
        }

        return [
            'type'                 => $type->value,
            'is_self'              => false,
            'is_friend'            => $isFriend,
            'is_following'         => $isFollowing,
            'is_followed_by'       => $isFollowedBy,
            'has_blocked'          => $hasBlocked,
            'is_blocked_by'        => $isBlockedBy,
            'friend_request'       => $friendRequestData,
            'mutual_friends_count' => $mutualCount,
        ];
    }
}
