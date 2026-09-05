<?php

namespace App\Modules\Chat\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Chat\Enums\ChatRoomTypeEnum;
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
            return $this->forbidden('You do not have access to this chat room.');
        }

        $pinned = $this->pinnedService->getPinnedMessages($room);

        return $this->success(
            PinnedMessageResource::collection($pinned),
            'Pinned messages retrieved successfully'
        );
    }

    /**
     * Pin a message in a room.
     */
    public function pin(ChatRoom $room, Message $message): JsonResponse
    {
        $user = auth('api')->user();

        if ($message->chat_room_id !== $room->id) {
            return $this->error('Message does not belong to this room.', null, 422);
        }

        if (!$this->permissionService->canView($user, $room)) {
            return $this->forbidden('You do not have access to this chat room.');
        }

        $participant = ChatParticipant::where('chat_room_id', $room->id)->where('user_id', $user->id)->first();
        if ($room->type !== ChatRoomTypeEnum::SINGLE && $participant && !in_array($participant->role, [ParticipantRoleEnum::OWNER, ParticipantRoleEnum::ADMIN])) {
            return $this->forbidden('Only admins or owners can pin messages in group/channel.');
        }

        $pinned = $this->pinnedService->pinMessage($room, $message, $user);

        return $this->created(
            new PinnedMessageResource($pinned),
            'Message pinned successfully'
        );
    }

    /**
     * Unpin a message in a room.
     */
    public function unpin(ChatRoom $room, Message $message): JsonResponse
    {
        $user = auth('api')->user();

        if ($message->chat_room_id !== $room->id) {
            return $this->error('Message does not belong to this room.', null, 422);
        }

        if (!$this->permissionService->canView($user, $room)) {
            return $this->forbidden('You do not have access to this chat room.');
        }

        $participant = ChatParticipant::where('chat_room_id', $room->id)->where('user_id', $user->id)->first();
        if ($room->type !== ChatRoomTypeEnum::SINGLE && $participant && !in_array($participant->role, [ParticipantRoleEnum::OWNER, ParticipantRoleEnum::ADMIN])) {
            return $this->forbidden('Only admins or owners can unpin messages.');
        }

        $this->pinnedService->unpinMessage($room, $message);

        return $this->success(null, 'Message unpinned successfully');
    }
}
