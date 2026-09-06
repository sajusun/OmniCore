<?php

namespace App\Modules\Chat\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Modules\Chat\Http\Requests\BlockUserRequest;
use App\Modules\Chat\Services\BlockService;

class BlockController extends Controller
{
    public function __construct(protected BlockService $blockService)
    {
        parent::__construct();
    }

    /**
     * Block a user.
     */
    public function block(User $user, BlockUserRequest $request)
    {
        if ($user->id === auth('api')->id()) {
            return $this->error('You cannot block yourself.', null, 422);
        }

        $this->blockService->block(auth('api')->id(), $user->id);

        return $this->success(
            message: 'User blocked successfully'
        );
    }

    /**
     * Unblock a user.
     */
    public function unblock(User $user)
    {
        $this->blockService->unblock(auth('api')->id(), $user->id);

        return $this->success(
            message: 'User unblocked successfully'
        );
    }

    /**
     * List all blocked users.
     */
    public function blockedUsers()
    {
        $blocked = $this->blockService->blockedUsers(auth('api')->id());

        return $this->success(
            data: UserResource::collection($blocked),
            message: 'Blocked users list retrieved successfully'
        );
    }
}
