<?php

namespace App\Modules\Chat\Services;

use App\Models\User;
use App\Modules\Chat\Enums\ChatRoomTypeEnum;
use App\Modules\Chat\Enums\ParticipantRoleEnum;
use App\Modules\Chat\Models\ChatParticipant;
use App\Modules\Chat\Models\ChatRoom;
use App\Modules\Chat\Models\Message;

class ChatPermissionService
{
    public function __construct(protected BlockService $blockService) {}

    /**
     * Determine if the user can create a room of a specific type.
     */
    public function canCreateRoom(User $user, ChatRoomTypeEnum $type, array $participantIds = []): bool
    {
        if ($type === ChatRoomTypeEnum::SINGLE) {
            if (count($participantIds) !== 1) {
                return false;
            }
            $targetUserId = $participantIds[0];
            if ($this->blockService->isBlocked($user->id, $targetUserId)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if the user can join the room.
     */
    public function canJoin(User $user, ChatRoom $room): bool
    {
        if ($room->type !== ChatRoomTypeEnum::CHANNEL) {
            return false;
        }

        if ($room->created_by && $this->blockService->isBlocked($user->id, $room->created_by)) {
            return false;
        }

        return true;
    }

    /**
     * Determine if the user can leave the room.
     */
    public function canLeave(User $user, ChatRoom $room): bool
    {
        return ChatParticipant::where('chat_room_id', $room->id)
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * Determine if the user can send a message in the room.
     */
    public function canSend(User $user, ChatRoom $room): bool
    {
        $participant = ChatParticipant::where('chat_room_id', $room->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$participant) {
            return false;
        }

        if ($room->type === ChatRoomTypeEnum::CHANNEL) {
            return in_array($participant->role, [ParticipantRoleEnum::OWNER, ParticipantRoleEnum::ADMIN]);
        }

        if ($room->type === ChatRoomTypeEnum::SINGLE) {
            $otherParticipant = ChatParticipant::where('chat_room_id', $room->id)
                ->where('user_id', '!=', $user->id)
                ->first();

            if ($otherParticipant) {
                if ($this->blockService->isBlocked($user->id, $otherParticipant->user_id)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Determine if the user can delete a resource (ChatRoom or Message).
     */
    public function canDelete(User $user, $target): bool
    {
        if ($target instanceof ChatRoom) {
            $participant = ChatParticipant::where('chat_room_id', $target->id)
                ->where('user_id', $user->id)
                ->first();

            return $participant && $participant->role === ParticipantRoleEnum::OWNER;
        }

        if ($target instanceof Message) {
            if ($target->sender_id === $user->id) {
                return true;
            }

            $participant = ChatParticipant::where('chat_room_id', $target->chat_room_id)
                ->where('user_id', $user->id)
                ->first();

            return $participant && in_array($participant->role, [ParticipantRoleEnum::OWNER, ParticipantRoleEnum::ADMIN]);
        }

        return false;
    }

    /**
     * Determine if the user can view the room content.
     */
    public function canView(User $user, ChatRoom $room): bool
    {
        return ChatParticipant::where('chat_room_id', $room->id)
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * Determine if the user can edit a resource (ChatRoom or Message).
     */
    public function canEdit(User $user, $target): bool
    {
        if ($target instanceof ChatRoom) {
            $participant = ChatParticipant::where('chat_room_id', $target->id)
                ->where('user_id', $user->id)
                ->first();

            return $participant && in_array($participant->role, [ParticipantRoleEnum::OWNER, ParticipantRoleEnum::ADMIN]);
        }

        if ($target instanceof Message) {
            return $target->sender_id === $user->id;
        }

        return false;
    }
}
