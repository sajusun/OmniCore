<?php

namespace App\Modules\Chat\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Modules\Chat\Enums\ParticipantRoleEnum;
use App\Modules\Chat\Http\Resources\PinnedMessageResource;
use App\Modules\Chat\Models\ChatParticipant;
use App\Modules\Chat\Models\ChatRoom;
use App\Modules\Chat\Models\Message;
use App\Modules\Chat\Services\ChatPermissionService;
use App\Modules\Chat\Services\PinnedMessageService;
use Illuminate\Http\JsonResponse;

class PinnedMessageController extends Controller
{
    public function __construct(
        protected PinnedMessageService $pinnedService,
        protected ChatPermissionService $permissionService
    ) {
        parent::__construct();
    }

    /**
     * Get all pinned messages in a room.
     */
    public function index(ChatRoom $room): JsonResponse
    {
        $user = auth('api')->user();

        if (!$this->permissionService->canView($user, $room)) {
            return Helper::jsonResponse(false, 'You do not have access to this chat room.', 403);
        }

        $pinned = $this->pinnedService->getPinnedMessages($room);

        return Helper::jsonResponse(
            true,
            'Pinned messages retrieved successfully',
            200,
            PinnedMessageResource::collection($pinned)
        );
    }

    /**
     * Pin a message in a room.
     */
    public function pin(ChatRoom $room, Message $message): JsonResponse
    {
        $user = auth('api')->user();

        if ($message->chat_room_id !== $room->id) {
            return Helper::jsonResponse(false, 'Message does not belong to this room.', 422);
        }

        if (!$this->permissionService->canView($user, $room)) {
            return Helper::jsonResponse(false, 'You do not have access to this chat room.', 403);
        }

        $participant = ChatParticipant::where('chat_room_id', $room->id)->where('user_id', $user->id)->first();
        if ($room->type !== \App\Modules\Chat\Enums\ChatRoomTypeEnum::SINGLE && $participant && !in_array($participant->role, [ParticipantRoleEnum::OWNER, ParticipantRoleEnum::ADMIN])) {
            return Helper::jsonResponse(false, 'Only admins or owners can pin messages in group/channel.', 403);
        }

        $pinned = $this->pinnedService->pinMessage($room, $message, $user);

        return Helper::jsonResponse(
            true,
            'Message pinned successfully',
            201,
            new PinnedMessageResource($pinned)
        );
    }

    /**
     * Unpin a message in a room.
     */
    public function unpin(ChatRoom $room, Message $message): JsonResponse
    {
        $user = auth('api')->user();

        if ($message->chat_room_id !== $room->id) {
            return Helper::jsonResponse(false, 'Message does not belong to this room.', 422);
        }

        if (!$this->permissionService->canView($user, $room)) {
            return Helper::jsonResponse(false, 'You do not have access to this chat room.', 403);
        }

        $participant = ChatParticipant::where('chat_room_id', $room->id)->where('user_id', $user->id)->first();
        if ($room->type !== \App\Modules\Chat\Enums\ChatRoomTypeEnum::SINGLE && $participant && !in_array($participant->role, [ParticipantRoleEnum::OWNER, ParticipantRoleEnum::ADMIN])) {
            return Helper::jsonResponse(false, 'Only admins or owners can unpin messages.', 403);
        }

        $this->pinnedService->unpinMessage($room, $message);

        return Helper::jsonResponse(true, 'Message unpinned successfully', 200);
    }
}
