<?php

namespace App\Modules\Call\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallEnded implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $callUuid,
        public int $endedById,
        public int $duration
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('call.' . $this->callUuid),
        ];
    }

    public function broadcastAs(): string
    {
        return 'call.ended';
    }
}
