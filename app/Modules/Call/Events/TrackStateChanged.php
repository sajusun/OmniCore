<?php

namespace App\Modules\Call\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TrackStateChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $callUuid,
        public int $userId,
        public bool $isMuted,
        public bool $isVideoEnabled,
        public bool $isScreenSharing
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('call.' . $this->callUuid),
        ];
    }

    public function broadcastAs(): string
    {
        return 'call.track_state';
    }
}
