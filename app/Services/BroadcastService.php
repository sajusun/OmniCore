<?php

namespace App\Services;

use App\Models\Notification;
use App\Events\NotificationCreated;

class BroadcastService
{
    public function send(Notification $notification): void
    {
        broadcast(new NotificationCreated($notification))->toOthers();
    }
}
