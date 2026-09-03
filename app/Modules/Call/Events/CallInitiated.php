<?php

namespace App\Modules\Call\Events;

use App\Modules\Call\Models\CallSession;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallInitiated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public CallSession $session,
        public int $targetUserId
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->targetUserId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'call.incoming';
    }

    public function broadcastWith(): array
    {
        return [
            'uuid'         => $this->session->uuid,
            'caller_id'    => $this->session->caller_id,
            'caller_name'  => $this->session->caller?->name,
            'caller_avatar'=> $this->session->caller?->avatar ? url($this->session->caller->avatar) : null,
            'type'         => $this->session->type->value,
            'channel_name' => $this->session->channel_name,
            'chat_room_id' => $this->session->chat_room_id,
        ];
    }
}
