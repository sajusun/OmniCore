<?php

use App\Modules\Chat\Http\Controllers\BlockController;
use App\Modules\Chat\Http\Controllers\ChatRoomController;
use App\Modules\Chat\Http\Controllers\ChatSettingController;
use App\Modules\Chat\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Chat API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:api')->prefix('chat')->group(function () {
    // Rooms
    Route::post('/rooms/single', [ChatRoomController::class, 'single']);
    Route::post('/rooms/group', [ChatRoomController::class, 'group']);
    Route::post('/rooms/channel', [ChatRoomController::class, 'channel']);
    Route::get('/rooms', [ChatRoomController::class, 'index']);
    Route::get('/rooms/{room}', [ChatRoomController::class, 'show']);
    Route::patch('/rooms/{room}', [ChatRoomController::class, 'update']);
    Route::delete('/rooms/{room}', [ChatRoomController::class, 'destroy']);

    // Participants
    Route::post('/rooms/{room}/participants', [ChatRoomController::class, 'addParticipants']);
    Route::delete('/rooms/{room}/participants/{user}', [ChatRoomController::class, 'removeParticipant']);
    Route::post('/rooms/{room}/leave', [ChatRoomController::class, 'leave']);
    Route::post('/channels/{room}/join', [ChatRoomController::class, 'join']);

    // Messages
    Route::get('/rooms/{room}/messages', [MessageController::class, 'index']);
    Route::post('/messages', [MessageController::class, 'send']);
    Route::patch('/messages/{message}', [MessageController::class, 'update']);
    Route::delete('/messages/{message}', [MessageController::class, 'destroy']);

    // Blocks
    Route::post('/block/{user}', [BlockController::class, 'block']);
    Route::delete('/unblock/{user}', [BlockController::class, 'unblock']);
    Route::get('/blocked-users', [BlockController::class, 'blockedUsers']);

    // Settings
    Route::patch('/rooms/{room}/settings/notification', [ChatSettingController::class, 'updateNotification']);
    Route::patch('/rooms/{room}/settings/sound', [ChatSettingController::class, 'updateSound']);
    Route::patch('/rooms/{room}/settings/mute', [ChatSettingController::class, 'mute']);
    Route::delete('/rooms/{room}/settings/mute', [ChatSettingController::class, 'unmute']);
});
