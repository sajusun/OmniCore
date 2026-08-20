<?php

namespace App\Services;

use App\Models\Notification;
use App\Events\NotificationCreated;

class BroadcastService
{
    public function send(Notification $notification): void
    {
        try {
            broadcast(new NotificationCreated($notification))->toOthers();
        } catch (\Throwable $th) {
            \Illuminate\Support\Facades\Log::warning('Broadcast notification skipped or failed: ' . $th->getMessage());
        }
    }
}
