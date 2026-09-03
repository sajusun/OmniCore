<?php

namespace App\Modules\Chat\Services;

use App\Modules\Chat\Enums\ChatRoomTypeEnum;
use App\Modules\Chat\Enums\ParticipantRoleEnum;
use App\Modules\Chat\Models\ChatRoom;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ChatRoomService
{
    public function __construct(
        protected RoomParticipantService $participantService,
        protected BlockService $blockService
    ) {}

    /**
     * Create a single room between two users.
     */
    public function createSingleRoom(int $creatorId, int $userId): ChatRoom
    {
        if ($creatorId === $userId) {
            throw new \InvalidArgumentException("You cannot create a single chat room with yourself.");
        }

        // Check blocks
        if ($this->blockService->isBlocked($creatorId, $userId)) {
            throw new \RuntimeException("Cannot create chat: User is blocked.");
        }

        // Check if exists
        $existing = $this->findExistingSingleRoom($creatorId, $userId);
        if ($existing) {
            return $existing;
        }

        return DB::transaction(function () use ($creatorId, $userId) {
            $room = ChatRoom::create([
                'type'       => ChatRoomTypeEnum::SINGLE->value,
                'created_by' => $creatorId,
            ]);

            // Add both participants
            $this->participantService->add($room, $creatorId, ParticipantRoleEnum::OWNER);
            $this->participantService->add($room, $userId, ParticipantRoleEnum::MEMBER);

            return $room;
        });
    }

    /**
     * Create a group chat.
     */
    public function createGroup(int $creatorId, string $name, array $participantIds, ?string $description = null, ?string $image = null): ChatRoom
    {
        return DB::transaction(function () use ($creatorId, $name, $participantIds, $description, $image) {
            $room = ChatRoom::create([
                'type'        => ChatRoomTypeEnum::GROUP->value,
                'name'        => $name,
                'description' => $description,
                'image'       => $image,
                'created_by'  => $creatorId,
            ]);

            // Creator is Owner
            $this->participantService->add($room, $creatorId, ParticipantRoleEnum::OWNER);

            // Add other participants, filtering out blocked ones
            foreach ($participantIds as $userId) {
                if ($userId !== $creatorId && !$this->blockService->isBlocked($creatorId, $userId)) {
                    $this->participantService->add($room, $userId, ParticipantRoleEnum::MEMBER);
                }
            }

            return $room;
        });
    }

    /**
     * Create a broadcast/announcement channel.
     */
    public function createChannel(int $creatorId, string $name, ?string $description = null, ?string $image = null): ChatRoom
    {
        return DB::transaction(function () use ($creatorId, $name, $description, $image) {
            $room = ChatRoom::create([
                'type'        => ChatRoomTypeEnum::CHANNEL->value,
                'name'        => $name,
                'description' => $description,
                'image'       => $image,
                'created_by'  => $creatorId,
            ]);

            // Creator is Owner
            $this->participantService->add($room, $creatorId, ParticipantRoleEnum::OWNER);

            return $room;
        });
    }

    /**
     * Update room details.
     */
    public function updateRoom(ChatRoom $room, array $data): ChatRoom
    {
        $room->update($data);
        return $room;
    }

    /**
     * Delete room and its dependencies inside a transaction.
     */
    public function deleteRoom(ChatRoom $room): bool
    {
        return DB::transaction(function () use ($room) {
            $room->messages()->delete();
            $room->participants()->delete();
            return $room->delete();
        });
    }

    /**
     * Retrieve all rooms the user is participating in.
     */
    public function rooms(int $userId, ?string $type = null, int $perPage = 15): LengthAwarePaginator
    {
        return ChatRoom::query()
            ->whereHas('participants', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->when($type, function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->with(['creator', 'users'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get details of a single room.
     */
    public function room(int $roomId): ChatRoom
    {
        return ChatRoom::with(['creator', 'users'])->findOrFail($roomId);
    }

    /**
     * Get participants in a room.
     */
    public function participants(ChatRoom $room): Collection
    {
        return $room->participants()->with('user')->get();
    }

    /**
     * Add multiple participants to a group/channel.
     */
    public function addParticipants(ChatRoom $room, array $userIds): void
    {
        if ($room->type === ChatRoomTypeEnum::SINGLE) {
            throw new \RuntimeException("Cannot add participants to a single chat room.");
        }

        DB::transaction(function () use ($room, $userIds) {
            foreach ($userIds as $userId) {
                if (!$this->blockService->isBlocked($room->created_by ?? auth()->id(), $userId)) {
                    $this->participantService->add($room, $userId, ParticipantRoleEnum::MEMBER);
                }
            }
        });
    }

    /**
     * Remove multiple participants from a room.
     */
    public function removeParticipants(ChatRoom $room, array $userIds): void
    {
        DB::transaction(function () use ($room, $userIds) {
            foreach ($userIds as $userId) {
                $this->participantService->remove($room, $userId);
            }
        });
    }

    /**
     * Leave a chat room.
     */
    public function leaveRoom(ChatRoom $room, int $userId): void
    {
        $this->participantService->remove($room, $userId);
    }

    /**
     * Join a broadcast channel.
     */
    public function joinChannel(ChatRoom $room, int $userId): void
    {
        if ($room->type !== ChatRoomTypeEnum::CHANNEL) {
            throw new \RuntimeException("Only channels can be joined directly.");
        }

        $this->participantService->add($room, $userId, ParticipantRoleEnum::MEMBER);
    }

    /**
     * Helper to find a single room containing exactly User A and User B.
     */
    public function findExistingSingleRoom(int $userAId, int $userBId): ?ChatRoom
    {
        return ChatRoom::where('type', ChatRoomTypeEnum::SINGLE->value)
            ->whereHas('participants', function ($q) use ($userAId) {
                $q->where('user_id', $userAId);
            })
            ->whereHas('participants', function ($q) use ($userBId) {
                $q->where('user_id', $userBId);
            })
            ->first();
    }

    /**
     * Get total unread messages count across all rooms for a user.
     */
    public function getTotalUnreadCount(int $userId): int
    {
        $participants = \App\Modules\Chat\Models\ChatParticipant::where('user_id', $userId)->get();
        $totalUnread = 0;

        foreach ($participants as $participant) {
            $lastReadId = $participant->last_read_message_id ?? 0;
            $totalUnread += \App\Modules\Chat\Models\Message::where('chat_room_id', $participant->chat_room_id)
                ->where('id', '>', $lastReadId)
                ->where('sender_id', '!=', $userId)
                ->count();
        }

        return $totalUnread;
    }
}
