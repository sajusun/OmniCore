<?php

namespace App\Modules\Notification\Services;

use App\Modules\Notification\Events\NotificationCreated;
use App\Modules\Notification\Models\Notification;
use Illuminate\Support\Facades\Log;

class BroadcastService
{
    public function send(Notification $notification): void
    {
        try {
            broadcast(new NotificationCreated($notification))->toOthers();
        } catch (\Throwable $th) {
            Log::warning('Broadcast notification skipped or failed: ' . $th->getMessage());
        }
    }
}
