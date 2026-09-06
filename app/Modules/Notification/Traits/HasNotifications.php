<?php

namespace App\Modules\Notification\Traits;

use App\Modules\Notification\Models\Notification;
use App\Modules\Notification\Services\NotificationService;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasNotifications
{
    public function appNotifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function unreadAppNotifications(): HasMany
    {
        return $this->appNotifications()->whereNull('read_at');
    }

    public function readAppNotifications(): HasMany
    {
        return $this->appNotifications()->whereNotNull('read_at');
    }

    public function unreadAppNotificationCount(): int
    {
        return $this->unreadAppNotifications()->count();
    }

    public function sendNotification(
        string $title,
        string $body,
        string $type = 'general',
        ?string $referenceType = null,
        string|int|null $referenceId = null,
        ?string $action = null,
        ?string $link = null,
        array $meta = []
    ): Notification {
        return app(NotificationService::class)->send(
            user: $this,
            title: $title,
            body: $body,
            type: $type,
            referenceType: $referenceType,
            referenceId: $referenceId,
            action: $action,
            link: $link,
            meta: $meta
        );
    }
}
