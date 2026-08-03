<?php

use App\Models\User;
use App\Models\ChatRoom;
use Illuminate\Support\Facades\Broadcast;
use App\Services\Chat\ChatPermissionService;

Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('chat.room.{roomId}', function (User $user, int $roomId) {
    $room = ChatRoom::find($roomId);
    return true;

    if (!$room) {
        return false;
    }

    return app(ChatPermissionService::class)->canView($user, $room);
});
