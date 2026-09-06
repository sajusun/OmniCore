<?php

namespace App\Modules\Notification\Repositories;

use App\Modules\Notification\Models\Notification;
use App\Modules\Notification\Repositories\Contracts\NotificationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class NotificationRepository implements NotificationRepositoryInterface
{
    public function create(array $data): Notification
    {
        return Notification::create($data);
    }

    public function getByUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return Notification::where('user_id', $userId)
            ->latest()
            ->paginate($perPage);
    }

    public function latest(int $userId, int $limit = 10): Collection
    {
        return Notification::where('user_id', $userId)
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function unreadCount(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->count();
    }

    public function markAsRead(string $id, int $userId): bool
    {
        return Notification::where('id', $id)
            ->where('user_id', $userId)
            ->update([
                'read_at' => now(),
            ]) > 0;
    }

    public function markAllAsRead(int $userId): bool
    {
        Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
            ]);

        return true;
    }

    public function delete(string $id, int $userId): bool
    {
        return Notification::where('id', $id)
            ->where('user_id', $userId)
            ->delete() > 0;
    }

    public function deleteAll(int $userId): bool
    {
        Notification::where('user_id', $userId)->delete();

        return true;
    }
}
