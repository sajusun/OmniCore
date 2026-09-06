<?php

namespace App\Modules\Notification\Repositories\Contracts;

use App\Modules\Notification\Models\Notification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface NotificationRepositoryInterface
{
    public function create(array $data): Notification;

    public function getByUser(int $userId, int $perPage = 15): LengthAwarePaginator;

    public function latest(int $userId, int $limit = 10): Collection;

    public function unreadCount(int $userId): int;

    public function markAsRead(string $id, int $userId): bool;

    public function markAllAsRead(int $userId): bool;

    public function delete(string $id, int $userId): bool;

    public function deleteAll(int $userId): bool;
}
