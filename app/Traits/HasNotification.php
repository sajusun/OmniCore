<?php

namespace App\Traits;

use App\Models\Notification;
use App\Events\NotificationCreated;

trait HasNotification
{
    public function sendNotification(
        string $title,
        string $body,
        string $type = 'general'
    ): Notification {

        $notification = Notification::create([
            'type'            => $type,
            'notifiable_type' => get_class($this),
            'notifiable_id'   => $this->id,
            'title'           => $title,
            'body'            => $body,
        ]);

        broadcast(new NotificationCreated($notification));

        return $notification;
    }

    public function markNotificationAsRead(string $notificationId): bool
    {
        return $this->notifications()->where('id', $notificationId)
            ->update([
                'read_at' => now()
            ]);
    }
}
