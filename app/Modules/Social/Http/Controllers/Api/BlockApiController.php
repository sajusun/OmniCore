<?php

namespace App\Modules\Social\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Social\Http\Resources\BlockedUserResource;
use App\Modules\Social\Services\BlockService;
use Illuminate\Http\JsonResponse;

class BlockApiController extends Controller
{
    public function __construct(private readonly BlockService $blockService)
    {
        parent::__construct();
    }

    public function index(): JsonResponse
    {
        $authUser = auth('api')->user() ?? auth()->user();
        $blockedUsers = $this->blockService->blockedUsers($authUser);

        return $this->paginated(
            $blockedUsers,
            BlockedUserResource::class,
            'Blocked users fetched successfully.'
        );
    }

    public function block(User $user): JsonResponse
    {
        $authUser = auth('api')->user() ?? auth()->user();
        $this->blockService->block($authUser, $user);

        return $this->success(null, 'User blocked successfully.');
    }

    public function unblock(User $user): JsonResponse
    {
        $authUser = auth('api')->user() ?? auth()->user();
        $this->blockService->unblock($authUser, $user);

        return $this->success(null, 'User unblocked successfully.');
    }
}
