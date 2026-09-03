<?php

namespace App\Modules\Chat\Services;

use App\Models\User;
use App\Modules\Chat\Enums\MessageTypeEnum;
use App\Modules\Chat\Events\MessagesRead;
use App\Modules\Chat\Events\MessageSent;
use App\Modules\Chat\Events\UserTyping;
use App\Modules\Chat\Models\ChatParticipant;
use App\Modules\Chat\Models\ChatRoom;
use App\Modules\Chat\Models\Message;
use App\Modules\Media\Traits\HandlesMedia;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MessageService
{
    use HandlesMedia;

    /**
     * Send a message in a room.
     */
    public function send(ChatRoom $room, int $senderId, array $data): Message
    {
        return DB::transaction(function () use ($room, $senderId, $data) {
            $messageType = $data['message_type'] ?? MessageTypeEnum::TEXT->value;

            $hasFiles = ! empty($data['files']);
            if ($hasFiles && $messageType === MessageTypeEnum::TEXT->value) {
                $firstFile = is_array($data['files']) ? $data['files'][0] : $data['files'];
                $mime = $firstFile->getMimeType();
                if (str_starts_with($mime, 'image/')) {
                    $messageType = MessageTypeEnum::IMAGE->value;
                } elseif (str_starts_with($mime, 'video/')) {
                    $messageType = MessageTypeEnum::VIDEO->value;
                } elseif (str_starts_with($mime, 'audio/')) {
                    $messageType = MessageTypeEnum::AUDIO->value;
                } else {
                    $messageType = MessageTypeEnum::DOCUMENT->value;
                }
            }

            $message = Message::create([
                'chat_room_id' => $room->id,
                'sender_id'    => $senderId,
                'message_type' => $messageType,
                'message'      => $data['message'] ?? null,
                'reply_to'     => $data['reply_to'] ?? null,
            ]);

            if ($hasFiles) {
                $this->uploadMedia($message, $data['files'], 'messages');
            }

            $room->participants()
                ->where('user_id', $senderId)
                ->update([
                    'last_read_message_id' => $message->id,
                    'last_read_at'         => now(),
                ]);

            $loadedMessage = $message->load(['sender', 'media', 'replyMessage.sender', 'reactions.user', 'pinned']);

            try {
                broadcast(new MessageSent($loadedMessage))->toOthers();
            } catch (\Throwable $th) {
                Log::error('Chat broadcast error: ' . $th->getMessage());
            }

            return $loadedMessage;
        });
    }

    /**
     * Mark messages up to messageId as read in a room.
     */
    public function markAsRead(ChatRoom $room, User $user, int $messageId): void
    {
        $now = now();

        $room->participants()
            ->where('user_id', $user->id)
            ->where(function ($q) use ($messageId) {
                $q->whereNull('last_read_message_id')
                    ->orWhere('last_read_message_id', '<', $messageId);
            })
            ->update([
                'last_read_message_id' => $messageId,
                'last_read_at'         => $now,
            ]);

        try {
            broadcast(new MessagesRead($room->id, $user->id, $messageId, $now->toIso8601String()))->toOthers();
        } catch (\Throwable $th) {
            Log::error('MessagesRead broadcast error: ' . $th->getMessage());
        }
    }

    /**
     * Get participants who have read a specific message.
     */
    public function getReadReceipts(Message $message)
    {
        return ChatParticipant::where('chat_room_id', $message->chat_room_id)
            ->where('user_id', '!=', $message->sender_id)
            ->where('last_read_message_id', '>=', $message->id)
            ->with('user')
            ->get();
    }

    /**
     * Broadcast user typing indicator.
     */
    public function broadcastTyping(ChatRoom $room, User $user, bool $isTyping): void
    {
        try {
            broadcast(new UserTyping($room->id, $user->id, $user->name, $isTyping))->toOthers();
        } catch (\Throwable $th) {
            Log::error('UserTyping broadcast error: ' . $th->getMessage());
        }
    }

    /**
     * Forward multiple messages to multiple target chat rooms.
     */
    public function forwardMessages(User $sender, array $messageIds, array $targetRoomIds): array
    {
        $messages = Message::whereIn('id', $messageIds)->with(['media'])->get();
        $createdMessages = [];

        DB::transaction(function () use ($sender, $messages, $targetRoomIds, &$createdMessages) {
            foreach ($targetRoomIds as $roomId) {
                $room = ChatRoom::find($roomId);
                if (!$room) continue;

                foreach ($messages as $msg) {
                    $newMsg = Message::create([
                        'chat_room_id' => $roomId,
                        'sender_id'    => $sender->id,
                        'message_type' => $msg->message_type,
                        'message'      => $msg->message,
                    ]);

                    // Clone media if exists
                    if ($msg->media()->exists()) {
                        foreach ($msg->media as $mediaItem) {
                            $newMsg->media()->create([
                                'collection_name' => $mediaItem->collection_name,
                                'file_name'       => $mediaItem->file_name,
                                'mime_type'       => $mediaItem->mime_type,
                                'disk'            => $mediaItem->disk,
                                'size'            => $mediaItem->size,
                                'path'            => $mediaItem->path,
                            ]);
                        }
                    }

                    $loaded = $newMsg->load(['sender', 'media', 'reactions']);
                    $createdMessages[] = $loaded;

                    try {
                        broadcast(new MessageSent($loaded))->toOthers();
                    } catch (\Throwable $th) {
                        Log::error('Forward broadcast error: ' . $th->getMessage());
                    }
                }
            }
        });

        return $createdMessages;
    }

    /**
     * Search messages inside a room.
     */
    public function search(ChatRoom $room, string $query, int $perPage = 20): LengthAwarePaginator
    {
        return Message::where('chat_room_id', $room->id)
            ->where('message', 'LIKE', "%{$query}%")
            ->with(['sender', 'media', 'replyMessage.sender', 'reactions.user'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get shared media inside a room filtered by type.
     */
    public function sharedMedia(ChatRoom $room, ?string $type = null, int $perPage = 20): LengthAwarePaginator
    {
        $query = Message::where('chat_room_id', $room->id)
            ->whereHas('media')
            ->with(['sender', 'media']);

        if ($type) {
            $query->where('message_type', $type);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Update a message.
     */
    public function update(Message $message, array $data): Message
    {
        $message->update([
            'message'   => $data['message'],
            'is_edited' => true,
            'edited_at' => now(),
        ]);

        return $message->load(['sender', 'media', 'replyMessage.sender', 'reactions.user', 'pinned']);
    }

    /**
     * Delete a message.
     */
    public function delete(Message $message): bool
    {
        return DB::transaction(function () use ($message) {
            if ($message->media()->exists()) {
                $mediaIds = $message->media->pluck('id')->toArray();
                $this->deleteMedia($mediaIds);
            }

            return $message->delete();
        });
    }

    /**
     * Get paginated messages for a chat room with delta sync support.
     */
    public function messages(ChatRoom $room, ?int $perPage = 15, ?int $afterId = null): LengthAwarePaginator
    {
        $query = Message::where('chat_room_id', $room->id)
            ->with(['sender', 'media', 'replyMessage.sender', 'reactions.user', 'pinned']);

        if ($afterId) {
            $query->where('id', '>', $afterId);
        }

        return $query->latest()->paginate($perPage ?? 15);
    }
}
