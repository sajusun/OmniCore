<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Follower;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasFollowers
{

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
}