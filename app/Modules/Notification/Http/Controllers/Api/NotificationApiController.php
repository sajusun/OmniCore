<?php

namespace App\Modules\Notification\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Notification\Http\Resources\NotificationResource;
use App\Modules\Notification\Models\Notification;
use App\Modules\Notification\Repositories\Contracts\NotificationRepositoryInterface;
use App\Modules\Notification\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationApiController extends Controller
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
            auth('api')->id() ?? auth()->id(),
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
            'count' => $this->service->unreadCount(auth('api')->id() ?? auth()->id()),
        ], 'Unread count fetched successfully.');
    }

    /**
     * Mark as Read
     */
    public function markAsRead(Notification|string $notification): JsonResponse
    {
        $authId = auth('api')->id() ?? auth()->id();
        $notificationModel = $notification instanceof Notification
            ? $notification
            : Notification::findOrFail($notification);

        abort_if($notificationModel->user_id != $authId, 403);

        $this->service->markAsRead(
            $notificationModel->id,
            $authId
        );

        return $this->success(null, 'Notification marked as read.');
    }

    /**
     * Mark All Read
     */
    public function markAllAsRead(): JsonResponse
    {
        $this->service->markAllAsRead(auth('api')->id() ?? auth()->id());

        return $this->success(null, 'All notifications marked as read.');
    }

    /**
     * Delete Notification
     */
    public function destroy(Notification|string $notification): JsonResponse
    {
        $authId = auth('api')->id() ?? auth()->id();
        $notificationModel = $notification instanceof Notification
            ? $notification
            : Notification::findOrFail($notification);

        abort_if($notificationModel->user_id != $authId, 403);

        $deleted = $this->service->delete(
            $notificationModel->id,
            $authId
        );

        if (! $deleted) {
            return $this->error('Notification not found.', null, 404);
        }

        return $this->success(null, 'Notification deleted successfully.');
    }

    /**
     * Delete All Notifications
     */
    public function destroyAll(): JsonResponse
    {
        $this->service->deleteAll(auth('api')->id() ?? auth()->id());

        return $this->success(null, 'All notifications deleted successfully.');
    }

    public function sendTestNotification(User $user, Request $request): JsonResponse
    {
        $this->service->send($user, $request->title, $request->body);

        return $this->success(null, 'Notification sent.');
    }
}
