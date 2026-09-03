<?php

namespace App\Modules\Social\Services;

use App\Models\User;
use App\Services\BaseService;
use Illuminate\Validation\ValidationException;

class BlockService extends BaseService
{
    public function block(User $authUser, User $user): void
    {
        if ($authUser->id === $user->id) {
            throw ValidationException::withMessages([
                'user' => 'You cannot block yourself.',
            ]);
        }

        $authUser->block($user);
    }

    public function unblock(User $authUser, User $user): void
    {
        $authUser->unblock($user);
    }

    public function blockedUsers(User $authUser)
    {
        return $this->applyPagination($authUser->blockedUsers()->latest('user_blocks.created_at'));
    }
}
