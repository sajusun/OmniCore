<?php

namespace App\Services\Chat;

use App\Models\User;
use App\Models\ChatRoom;
use App\Models\Message;
use App\Models\ChatParticipant;
use App\Enums\Chat\ChatRoomTypeEnum;
use App\Enums\Chat\ParticipantRoleEnum;

class ChatPermissionService
{
    public function __construct(protected BlockService $blockService)
    {
    }

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
            // Check if blocked by target, or if sender blocked target
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
        // Only public channels can be joined directly.
        if ($room->type !== ChatRoomTypeEnum::CHANNEL) {
            return false;
        }

        // If creator blocked them or they blocked creator, block join
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
        // Must be a participant to leave
        $isParticipant = ChatParticipant::where('chat_room_id', $room->id)
            ->where('user_id', $user->id)
            ->exists();

        if (!$isParticipant) {
            return false;
        }

        // Owner/Creator of a group/channel should not be able to leave without deleting it,
        // or they can leave if there are other admins. For simplicity, we allow it.
        return true;
    }

    /**
     * Determine if the user can send a message in the room.
     */
    public function canSend(User $user, ChatRoom $room): bool
    {
        // Must be a participant (unless it's a channel and they are joining/sending, but in channels only admins/creators send)
        $participant = ChatParticipant::where('chat_room_id', $room->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$participant) {
            return false;
        }

        if ($room->type === ChatRoomTypeEnum::CHANNEL) {
            // Only owners and admins can send in channels
            return in_array($participant->role, [ParticipantRoleEnum::OWNER, ParticipantRoleEnum::ADMIN]);
        }

        if ($room->type === ChatRoomTypeEnum::SINGLE) {
            // Find the other participant
            $otherParticipant = ChatParticipant::where('chat_room_id', $room->id)
                ->where('user_id', '!=', $user->id)
                ->first();

            if ($otherParticipant) {
                // If either user has blocked the other, message cannot be sent
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
            // Only the owner/creator can delete the room
            $participant = ChatParticipant::where('chat_room_id', $target->id)
                ->where('user_id', $user->id)
                ->first();

            return $participant && $participant->role === ParticipantRoleEnum::OWNER;
        }

        if ($target instanceof Message) {
            // Sender can delete their own message.
            if ($target->sender_id === $user->id) {
                return true;
            }

            // Chat room owner/admin can delete any message.
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
        // For channels, non-participants might view it if public, but for general messaging they must join.
        // Let's require the user to be a participant to view.
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
            // Only owners and admins can edit room info.
            $participant = ChatParticipant::where('chat_room_id', $target->id)
                ->where('user_id', $user->id)
                ->first();

            return $participant && in_array($participant->role, [ParticipantRoleEnum::OWNER, ParticipantRoleEnum::ADMIN]);
        }

        if ($target instanceof Message) {
            // Only the sender can edit their own message.
            return $target->sender_id === $user->id;
        }

        return false;
    }
}
