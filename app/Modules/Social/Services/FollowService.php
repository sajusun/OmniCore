<?php

namespace App\Modules\Social\Services;

use App\Models\User;
use App\Services\BaseService;
use Illuminate\Validation\ValidationException;

class FollowService extends BaseService
{
    public function follow(User $authUser, User $user): void
    {
        if ($authUser->id === $user->id) {
            throw ValidationException::withMessages([
                'user' => 'You cannot follow yourself.',
            ]);
        }

        if ($authUser->hasBlocked($user) || $authUser->isBlockedBy($user)) {
            throw ValidationException::withMessages([
                'user' => 'Cannot follow this user.',
            ]);
        }

        $authUser->follow($user);
    }

    public function unfollow(User $authUser, User $user): void
    {
        $authUser->unfollow($user);
    }

    public function toggle(User $authUser, User $user): bool
    {
        if ($authUser->id === $user->id) {
            throw ValidationException::withMessages([
                'user' => 'You cannot follow yourself.',
            ]);
        }

        if ($authUser->hasBlocked($user) || $authUser->isBlockedBy($user)) {
            throw ValidationException::withMessages([
                'user' => 'Cannot follow this user.',
            ]);
        }

        if ($authUser->isFollowing($user)) {
            $authUser->unfollow($user);
            return false;
        }

        $authUser->follow($user);
        return true;
    }

    public function followers(User $user)
    {
        return $this->applyPagination($user->followers()->latest('followers.created_at'));
    }

    public function followings(User $user)
    {
        return $this->applyPagination($user->followings()->latest('followers.created_at'));
    }
}
