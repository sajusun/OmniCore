<?php

namespace App\Modules\Chat\Services;

use App\Models\User;
use App\Modules\Chat\Events\MessagePinned;
use App\Modules\Chat\Models\ChatRoom;
use App\Modules\Chat\Models\Message;
use App\Modules\Chat\Models\PinnedMessage;
use Illuminate\Support\Facades\Log;

class PinnedMessageService
{
    /**
     * Pin a message in a chat room.
     */
    public function pinMessage(ChatRoom $room, Message $message, User $user): PinnedMessage
    {
        $pinned = PinnedMessage::firstOrCreate(
            [
                'chat_room_id' => $room->id,
                'message_id'   => $message->id,
            ],
            [
                'pinned_by_id' => $user->id,
                'pinned_at'    => now(),
            ]
        );

        try {
            broadcast(new MessagePinned($room->id, $message->id, true, $user->id))->toOthers();
        } catch (\Throwable $th) {
            Log::error('Pin broadcast error: ' . $th->getMessage());
        }

        return $pinned->load(['pinnedBy', 'message.sender', 'message.media']);
    }

    /**
     * Unpin a message from a chat room.
     */
    public function unpinMessage(ChatRoom $room, Message $message): void
    {
        PinnedMessage::where('chat_room_id', $room->id)
            ->where('message_id', $message->id)
            ->delete();

        try {
            broadcast(new MessagePinned($room->id, $message->id, false))->toOthers();
        } catch (\Throwable $th) {
            Log::error('Unpin broadcast error: ' . $th->getMessage());
        }
    }

    /**
     * Get all pinned messages in a room.
     */
    public function getPinnedMessages(ChatRoom $room)
    {
        return $room->pinnedMessages()
            ->with(['pinnedBy', 'message.sender', 'message.media', 'message.reactions'])
            ->latest('pinned_at')
            ->get();
    }
}
