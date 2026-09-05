<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Models\User;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(
        protected NotificationRepositoryInterface $repository,
        protected NotificationService $service
    ) {
        parent::__construct();
    }

    /**
     * Notification List
     */
    public function index(Request $request): JsonResponse
    {
        $notifications = $this->repository->getByUser(
            auth()->id(),
            $request->integer('per_page', 15)
        );

        return $this->paginated($notifications, NotificationResource::class, 'Notifications fetched successfully.');
    }

    /**
     * Unread Count
     */
    public function unreadCount(): JsonResponse
    {
        return $this->success([
            'count' => $this->service->unreadCount(auth()->id()),
        ], 'Unread count fetched successfully.');
    }

    /**
     * Mark as Read
     */
    public function markAsRead(Notification $notification): JsonResponse
    {
        abort_if($notification->user_id != auth()->id(), 403);

        $this->service->markAsRead(
            $notification->id,
            auth()->id()
        );

        return $this->success(null, 'Notification marked as read.');
    }

    /**
     * Mark All Read
     */
    public function markAllAsRead(): JsonResponse
    {
        $this->service->markAllAsRead(auth()->id());

        return $this->success(null, 'All notifications marked as read.');
    }

    /**
     * Delete Notification
     */
    public function destroy(Notification $notification): JsonResponse
    {
        abort_if($notification->user_id !== auth()->id(), 403);

        $deleted = $this->service->delete(
            $notification->id,
            auth()->id()
        );

        if (!$deleted) {
            return $this->error('Notification not found.', null, 404);
        }

        return $this->success(null, 'Notification deleted successfully.');
    }

    /**
     * Delete All Notifications
     */
    public function destroyAll(): JsonResponse
    {
        $this->service->deleteAll(auth()->id());

        return $this->success(null, 'All notifications deleted successfully.');
    }

    public function sendTestNotification(User $user, Request $request): JsonResponse
    {
        $this->service->send($user, $request->title, $request->body);

        return $this->success(null, 'Notification sent.');
    }
}
