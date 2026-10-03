<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthenticated'
                ], 401);
            }

            $perPage = (int) $request->input('per_page', 10);
            $notifications = $user->appNotifications()
                ->latest()
                ->cursorPaginate($perPage);

            $items = $notifications->items();

            return response()->json([
                'status'       => 'success',
                'message'      => 'Notifications retrieved successfully.',
                'data'         => [
                    'data'        => $items,
                    'next_cursor' => $notifications->nextCursor()?->encode(),
                    'prev_cursor' => $notifications->previousCursor()?->encode(),
                    'has_more'    => $notifications->hasMorePages(),
                ],
                'next_cursor'  => $notifications->nextCursor()?->encode(),
                'prev_cursor'  => $notifications->previousCursor()?->encode(),
                'has_more'     => $notifications->hasMorePages(),
                'unread_count' => $user->unreadAppNotifications()->count(),
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function readSingle($id)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'code' => 401,
                    'status' => 'error',
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            $notification = $user->appNotifications()->find($id);
            if (!$notification) {
                return response()->json([
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'Notification not found.',
                ], 404);
            }

            if (method_exists($notification, 'markAsRead')) {
                $notification->markAsRead();
            } else {
                $notification->update(['read_at' => now()]);
            }

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Notification marked as read.',
                'unread_count' => $user->unreadAppNotifications()->count(),
                'data' => $notification
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'code' => 401,
                    'status' => 'error',
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            $notification = $user->appNotifications()->find($id);
            if (!$notification) {
                return response()->json([
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'Notification not found.',
                ], 404);
            }

            $notification->delete();

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Notification deleted successfully.',
                'unread_count' => $user->unreadAppNotifications()->count(),
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function readAll()
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'code' => 401,
                    'status' => 'error',
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            $user->unreadAppNotifications()->update(['read_at' => now()]);

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'All notifications have been marked as read.',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

