<?php

namespace App\Modules\Chat\Services;

use App\Modules\Chat\Enums\ParticipantRoleEnum;
use App\Modules\Chat\Models\ChatParticipant;
use App\Modules\Chat\Models\ChatRoom;
use Illuminate\Support\Collection;

class RoomParticipantService
{
    /**
     * Add a participant to a room.
     */
    public function add(ChatRoom $room, int $userId, ParticipantRoleEnum $role = ParticipantRoleEnum::MEMBER): ChatParticipant
    {
        return ChatParticipant::updateOrCreate(
            [
                'chat_room_id' => $room->id,
                'user_id'      => $userId,
            ],
            [
                'role'                 => $role,
                'joined_at'            => now(),
                'notification_enabled' => true,
                'sound_enabled'        => true,
            ]
        );
    }

    /**
     * Remove a participant from a room.
     */
    public function remove(ChatRoom $room, int $userId): void
    {
        ChatParticipant::where([
            'chat_room_id' => $room->id,
            'user_id'      => $userId,
        ])->delete();
    }

    /**
     * Promote a participant to Admin.
     */
    public function makeAdmin(ChatRoom $room, int $userId): void
    {
        ChatParticipant::where([
            'chat_room_id' => $room->id,
            'user_id'      => $userId,
        ])->update(['role' => ParticipantRoleEnum::ADMIN->value]);
    }

    /**
     * Demote an Admin to Member.
     */
    public function removeAdmin(ChatRoom $room, int $userId): void
    {
        ChatParticipant::where([
            'chat_room_id' => $room->id,
            'user_id'      => $userId,
        ])->update(['role' => ParticipantRoleEnum::MEMBER->value]);
    }

    /**
     * Get the owner of the chat room.
     */
    public function owner(ChatRoom $room): ?ChatParticipant
    {
        return ChatParticipant::where([
            'chat_room_id' => $room->id,
            'role'         => ParticipantRoleEnum::OWNER->value,
        ])->first();
    }

    /**
     * Get all admins of the chat room.
     */
    public function admins(ChatRoom $room): Collection
    {
        return ChatParticipant::where('chat_room_id', $room->id)
            ->where('role', ParticipantRoleEnum::ADMIN->value)
            ->get();
    }

    /**
     * Get all regular members of the chat room.
     */
    public function members(ChatRoom $room): Collection
    {
        return ChatParticipant::where('chat_room_id', $room->id)
            ->where('role', ParticipantRoleEnum::MEMBER->value)
            ->get();
    }
}
