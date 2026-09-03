<?php

namespace App\Modules\Chat\Services;

use App\Modules\Chat\Enums\MessageTypeEnum;
use App\Modules\Chat\Events\MessageSent;
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

            $loadedMessage = $message->load(['sender', 'media', 'replyMessage']);

            try {
                broadcast(new MessageSent($loadedMessage))->toOthers();
            } catch (\Throwable $th) {
                Log::error('Chat broadcast error: ' . $th->getMessage());
            }

            return $loadedMessage;
        });
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

        return $message->load(['sender', 'media', 'replyMessage']);
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
     * Get paginated messages for a chat room.
     */
    public function messages(ChatRoom $room, ?int $perPage = 15): LengthAwarePaginator
    {
        return Message::where('chat_room_id', $room->id)
            ->with(['sender', 'media', 'replyMessage.sender'])
            ->latest()
            ->paginate($perPage ?? 15);
    }

    /**
     * Get latest message in a chat room.
     */
    public function latestMessage(ChatRoom $room): ?Message
    {
        return Message::where('chat_room_id', $room->id)
            ->with(['sender', 'media'])
            ->latest()
            ->first();
    }
}
