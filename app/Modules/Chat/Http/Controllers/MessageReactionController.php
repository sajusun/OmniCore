<?php

namespace App\Modules\Chat\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Modules\Chat\Http\Requests\ReactionRequest;
use App\Modules\Chat\Http\Resources\MessageReactionResource;
use App\Modules\Chat\Models\Message;
use App\Modules\Chat\Services\ChatPermissionService;
use App\Modules\Chat\Services\MessageReactionService;
use Illuminate\Http\JsonResponse;

class MessageReactionController extends Controller
{
    public function __construct(
        protected MessageReactionService $reactionService,
        protected ChatPermissionService $permissionService
    ) {
        parent::__construct();
    }

    /**
     * Toggle reaction on a message.
     */
    public function toggle(Message $message, ReactionRequest $request): JsonResponse
    {
        $user = auth('api')->user();

        if (!$this->permissionService->canView($user, $message->room)) {
            return Helper::jsonResponse(false, 'You do not have access to this chat room.', 403);
        }

        $result = $this->reactionService->toggleReaction($message, $user, $request->validated('reaction'));

        return Helper::jsonResponse(
            true,
            'Reaction updated successfully',
            200,
            $result
        );
    }

    /**
     * List all reactions for a message.
     */
    public function index(Message $message): JsonResponse
    {
        $user = auth('api')->user();

        if (!$this->permissionService->canView($user, $message->room)) {
            return Helper::jsonResponse(false, 'You do not have access to this chat room.', 403);
        }

        $reactions = $this->reactionService->getReactions($message);

        return Helper::jsonResponse(
            true,
            'Message reactions retrieved successfully',
            200,
            MessageReactionResource::collection($reactions)
        );
    }
}
