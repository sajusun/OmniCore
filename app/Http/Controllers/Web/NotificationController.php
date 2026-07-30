<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthenticated'
                ], 401);
            }

            $notifications = $user->appNotifications()->latest()->paginate(10);
            return response()->json([
                'status' => 'success',
                'message' => 'Your action was successful!',
                'data' => $notifications
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
                    'message' => 'Item not found.',
                ], 404);
            }

            $notification->markAsRead();
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Your action was successful!',
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

