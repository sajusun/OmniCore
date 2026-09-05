<?php

use App\Models\User;
use App\Modules\Chat\Models\ChatRoom;
use App\Modules\Chat\Services\ChatPermissionService;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('chat.room.{roomId}', function (User $user, int $roomId) {
    $room = ChatRoom::find($roomId);

    if (!$room) {
        return false;
    }

    return app(ChatPermissionService::class)->canView($user, $room);
});
