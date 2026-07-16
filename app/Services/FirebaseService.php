<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Str;
use App\Models\Notification;
use Kreait\Firebase\Factory;
use App\Models\FirebaseToken;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\Message;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

class FirebaseService
{
    protected  $messaging;

    public function __construct()
    {
        $this->messaging = (new Factory)->withServiceAccount(storage_path(config('firebase.credentials')))->createMessaging();
    }

    public function send(Notification $notification): void
    {
        $tokens = FirebaseToken::where('user_id', $notification->user_id)->pluck('token');

        if ($tokens->isEmpty()) {
            return;
        }

        foreach ($tokens as $token) {

            try {

                $firebaseNotification = FirebaseNotification::create($notification->title, Str::limit($notification->body, 100));

                $message = CloudMessage::withTarget('token', $token)
                    ->withNotification($firebaseNotification)
                    ->withData([
                        'id' => (string) $notification->id,
                        'type' => $notification->type,
                        'reference_type' => (string) $notification->reference_type,
                        'reference_id' => (string) $notification->reference_id,
                        'action' => (string) $notification->action,
                        'link' => (string) $notification->link,
                    ]);

                $this->messaging->send($message);
            } catch (Exception $e) {

                Log::error($e->getMessage());
            }
        }
    }
}
