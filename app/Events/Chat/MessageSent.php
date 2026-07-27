<?php

namespace App\Events\Chat;

use App\Models\Message;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use App\Http\Resources\Chat\MessageResource;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $message;

    public function __construct(Message $message)
    {
        $this->message = (new MessageResource($message))->resolve();
    }


    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.room.' . $this->message['chat_room_id']),
        ];
    }


    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}