<?php

namespace App\Modules\Notification\Jobs;

use App\Modules\Notification\Models\Notification;
use App\Modules\Notification\Services\FirebaseService;
use App\Modules\Notification\Services\BroadcastService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendPushNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 10;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Notification $notification
    ) {}

    /**
     * Execute the job.
     */
    public function handle(FirebaseService $firebaseService, BroadcastService $broadcastService): void
    {
        try {
            if (config('notifications.channels.firebase', true)) {
                $firebaseService->send($this->notification);
            }

            if (config('notifications.channels.broadcast', true)) {
                $broadcastService->send($this->notification);
            }
        } catch (Throwable $e) {
            Log::error('SendPushNotificationJob failed: ' . $e->getMessage(), [
                'notification_id' => $this->notification->id,
                'user_id' => $this->notification->user_id,
            ]);
            throw $e;
        }
    }
}
