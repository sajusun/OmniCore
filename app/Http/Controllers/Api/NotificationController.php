<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use App\Http\Resources\NotificationResource;
use App\Repositories\Contracts\NotificationRepositoryInterface;

class NotificationController extends Controller
{
    public function __construct(
        protected NotificationRepositoryInterface $repository,
        protected NotificationService $service
    ) {}

    /**
     * Notification List
     */
    public function index(Request $request)
    {
        $notifications = $this->repository->getByUser(
            auth()->id(),
            $request->integer('per_page', 15)
        );

        return NotificationResource::collection($notifications);
    }

    /**
     * Unread Count
     */
    public function unreadCount()
    {
        return response()->json([
            'success' => true,
            'count' => $this->service->unreadCount(auth()->id()),
        ]);
    }

    /**
     * Mark as Read
     */
    public function markAsRead(Notification $notification)
    {
        abort_if($notification->user_id != auth()->id(), 403);

        $this->service->markAsRead(
            $notification->id,
            auth()->id()
        );

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read.',
        ]);
    }

    /**
     * Mark All Read
     */
    public function markAllAsRead()
    {
        $this->service->markAllAsRead(auth()->id());

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read.',
        ]);
    }

    /**
     * Delete Notification
     */
    public function destroy(Notification $notification)
    {
        abort_if($notification->user_id !== auth()->id(), 403);

        $deleted = $this->service->delete(
            $notification->id,
            auth()->id()
        );

        if (! $deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted successfully.',
        ]);
    }


    public function sendTestNotification(User $user, Request $request)
    {
        $this->service->send($user, $request->title, $request->body);
        return response()->json([
            "success" => true,
            "message" => "sended let me check",
        ]);
    }
}
