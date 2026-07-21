<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Friend;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasFriends
{

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
        $friendId = $friend instanceof User
            ? $friend->id
            : $friend;

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
        $friendId = $friend instanceof User
            ? $friend->id
            : $friend;

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
        $friendId = $friend instanceof User
            ? $friend->id
            : $friend;

        return Friend::where([
            'user_id' => $this->id,
            'friend_id' => $friendId,
        ])->exists();
    }

    public function friendsCount(): int
    {
        return $this->friends()->count();
    }
}