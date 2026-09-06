<?php

namespace App\Modules\Notification\Services;

use App\Models\User;
use App\Modules\Notification\Models\Notification;
use App\Modules\Notification\Repositories\Contracts\NotificationRepositoryInterface;

class NotificationService
{
    public function __construct(
        protected NotificationRepositoryInterface $notifications,
        protected FirebaseService $firebaseService,
        protected BroadcastService $broadcastService,
    ) {}

    public function send(
        User|int $user,
        string $title,
        string $body,
        string $type = 'general',
        ?string $referenceType = null,
        string|int|null $referenceId = null,
        ?string $action = null,
        ?string $link = null,
        array $meta = []
    ): Notification {
        $meta = array_merge([
            'icon' => asset('default/icons/bell.png'),
            'type' => $type,
        ], $meta);

        $notification = $this->notifications->create([
            'user_id'        => $user instanceof User ? $user->id : $user,
            'type'           => $type,
            'title'          => $title,
            'body'           => $body,
            'reference_type' => $referenceType,
            'reference_id'   => $referenceId,
            'action'         => $action,
            'link'           => $link,
            'meta'           => $meta,
        ]);

        // Firebase Push
        if (config('notifications.channels.firebase')) {
            $this->firebaseService->send($notification);
        }

        // Broadcast
        if (config('notifications.channels.broadcast')) {
            $this->broadcastService->send($notification);
        }

        return $notification;
    }

    public function sendMany(
        iterable $users,
        string $title,
        string $body,
        string $type = 'general',
        ?string $referenceType = null,
        string|int|null $referenceId = null,
        ?string $action = null,
        ?string $link = null,
        array $meta = []
    ): void {
        foreach ($users as $user) {
            $this->send(
                $user,
                $title,
                $body,
                $type,
                $referenceType,
                $referenceId,
                $action,
                $link,
                $meta
            );
        }
    }

    public function markAsRead(string $notificationId, int $userId): bool
    {
        return $this->notifications->markAsRead($notificationId, $userId);
    }

    public function markAllAsRead(int $userId): bool
    {
        return $this->notifications->markAllAsRead($userId);
    }

    public function unreadCount(int $userId): int
    {
        return $this->notifications->unreadCount($userId);
    }

    public function delete(string $notificationId, int $userId): bool
    {
        return $this->notifications->delete($notificationId, $userId);
    }

    public function deleteAll(int $userId): bool
    {
        return $this->notifications->deleteAll($userId);
    }
}
