<?php

namespace App\Modules\Chat\Http\Controllers;

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
            return $this->forbidden('You do not have access to this chat room.');
        }

        $result = $this->reactionService->toggleReaction($message, $user, $request->validated('reaction'));

        return $this->success(
            $result,
            'Reaction updated successfully'
        );
    }

    /**
     * List all reactions for a message.
     */
    public function index(Message $message): JsonResponse
    {
        $user = auth('api')->user();

        if (!$this->permissionService->canView($user, $message->room)) {
            return $this->forbidden('You do not have access to this chat room.');
        }

        $reactions = $this->reactionService->getReactions($message);

        return $this->success(
            MessageReactionResource::collection($reactions),
            'Message reactions retrieved successfully'
        );
    }
}
