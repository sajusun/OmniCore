<?php

namespace App\Modules\Chat\Events;

use App\Modules\Chat\Http\Resources\MessageResource;
use App\Modules\Chat\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

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
