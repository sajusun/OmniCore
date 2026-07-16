<?php

namespace App\Http\Controllers\Api;

use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{

    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->latest()->paginate($request->per_page ?? 15);

        return response()->json([
            'status' => true,
            'message' => 'Notifications fetched successfully.',
            'data' => $notifications,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title'   => 'required|string|max:255',
            'body'    => 'required|string',
            'type'    => 'nullable|string|max:100',
        ]);

        $notification = Notification::create([
            'type'            => $request->type ?? 'general',
            'notifiable_type' => \App\Models\User::class,
            'notifiable_id'   => $request->user_id,
            'title'           => $request->title,
            'body'            => $request->body,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Notification sent successfully.',
            'data'    => $notification,
        ]);
    }

    public function markAsRead($id)
    {
        $notification = auth('api')->user()
            ->notifications()
            ->findOrFail($id);

        $notification->update([
            'read_at' => now(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Notification marked as read.',
        ]);
    }


    public function markAllAsRead(Request $request)
    {
        $request->user()->notifications()->whereNull('read_at')
            ->update([
                'read_at' => now(),
            ]);

        return response()->json([
            'status' => true,
            'message' => 'All notifications marked as read.',
        ]);
    }
}
