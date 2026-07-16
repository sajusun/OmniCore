<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class NotificationCreated implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Notification $notification
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->notification->user_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'notification.created';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->notification->id,
            'title' => $this->notification->title,
            'body' => $this->notification->body,
            'type' => $this->notification->type,
            'reference_type' => $this->notification->reference_type,
            'reference_id' => $this->notification->reference_id,
            'action' => $this->notification->action,
            'link' => $this->notification->link,
            'created_at' => $this->notification->created_at,
        ];
    }
}