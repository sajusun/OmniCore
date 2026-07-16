<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\BulkNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class NotificationController extends Controller
{
    public function create()
    {
        return view('backend.notifications.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $users = User::all();

        Notification::send($users, new BulkNotification($request->message, $request->subject));

        return redirect()->back()->with('success', 'Notification sent to all users successfully.');
    }
}
