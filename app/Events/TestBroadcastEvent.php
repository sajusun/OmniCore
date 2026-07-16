<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class TestBroadcastEvent implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $message
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('test-channel'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'test-message';
    }

    public function broadcastWith(): array
    {
        return [
            'message' => $this->message,
            'time'    => now()->toDateTimeString(),
        ];
    }
}