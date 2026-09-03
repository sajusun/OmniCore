<?php

namespace App\Modules\Social\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Social\Http\Resources\BlockedUserResource;
use App\Modules\Social\Services\BlockService;
use Illuminate\Http\JsonResponse;

class BlockApiController extends Controller
{
    public function __construct(private readonly BlockService $blockService) {}

    public function index(): JsonResponse
    {
        $authUser = auth('api')->user() ?? auth()->user();
        $blockedUsers = $this->blockService->blockedUsers($authUser);

        return Helper::jsonResponse(
            true,
            'Blocked users fetched successfully.',
            200,
            BlockedUserResource::collection($blockedUsers),
            [
                'current_page' => $blockedUsers->currentPage(),
                'last_page'    => $blockedUsers->lastPage(),
                'total'        => $blockedUsers->total(),
            ]
        );
    }

    public function block(User $user): JsonResponse
    {
        $authUser = auth('api')->user() ?? auth()->user();
        $this->blockService->block($authUser, $user);

        return Helper::jsonResponse(true, 'User blocked successfully.', 200);
    }

    public function unblock(User $user): JsonResponse
    {
        $authUser = auth('api')->user() ?? auth()->user();
        $this->blockService->unblock($authUser, $user);

        return Helper::jsonResponse(true, 'User unblocked successfully.', 200);
    }
}
