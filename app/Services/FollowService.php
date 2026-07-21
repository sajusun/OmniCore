<?php

namespace App\Services;

use App\Models\User;
use App\Services\BaseService;

class FollowService extends BaseService
{
    public function follow(User $authUser, User $user): void
    {
        if ($authUser->id === $user->id) {
            abort(422, 'You cannot follow yourself.');
        }

        $authUser->follow($user);
    }

    public function unfollow(User $authUser, User $user): void
    {
        $authUser->unfollow($user);
    }

    public function toggle(User $authUser, User $user): bool
    {
        if ($authUser->isFollowing($user)) {
            $authUser->unfollow($user);

            return false;
        }

        $authUser->follow($user);

        return true;
    }

    public function followers(User $user)
    {
        return $this->applyPagination($user->followers());
    }

    public function followings(User $user)
    {
        return $this->applyPagination($user->followings());
    }
}
