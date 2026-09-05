<?php

use App\Modules\Chat\Http\Controllers\BlockController;
use App\Modules\Chat\Http\Controllers\ChatRoomController;
use App\Modules\Chat\Http\Controllers\ChatSettingController;
use App\Modules\Chat\Http\Controllers\MessageController;
use App\Modules\Chat\Http\Controllers\MessageReactionController;
use App\Modules\Chat\Http\Controllers\PinnedMessageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Telegram / Messenger-Grade Chat API Routes (Protected via auth:api)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:api')->prefix('chat')->group(function () {
    // ─── Global Unread Counter Badge ──────────────────────────────────
    Route::get('/unread-count', [ChatRoomController::class, 'unreadCount']);

    // ─── Chat Rooms ───────────────────────────────────────────────────
    Route::get('/rooms', [ChatRoomController::class, 'index']);
    Route::post('/rooms/single', [ChatRoomController::class, 'single']);
    Route::post('/rooms/group', [ChatRoomController::class, 'group']);
    Route::post('/rooms/channel', [ChatRoomController::class, 'channel']);
    Route::get('/rooms/{room}', [ChatRoomController::class, 'show']);
    Route::patch('/rooms/{room}', [ChatRoomController::class, 'update']);
    Route::delete('/rooms/{room}', [ChatRoomController::class, 'destroy']);

    // ─── Room Participants & Channels ─────────────────────────────────
    Route::post('/rooms/{room}/participants', [ChatRoomController::class, 'addParticipants']);
    Route::delete('/rooms/{room}/participants/{user}', [ChatRoomController::class, 'removeParticipant']);
    Route::post('/rooms/{room}/leave', [ChatRoomController::class, 'leave']);
    Route::post('/channels/{room}/join', [ChatRoomController::class, 'join']);

    // ─── In-Chat Search & Shared Media ────────────────────────────────
    Route::get('/rooms/{room}/search', [MessageController::class, 'search']);
    Route::get('/rooms/{room}/media', [MessageController::class, 'sharedMedia']);

    // ─── Pinned Messages ──────────────────────────────────────────────
    Route::get('/rooms/{room}/pinned', [PinnedMessageController::class, 'index']);
    Route::post('/rooms/{room}/pin/{message}', [PinnedMessageController::class, 'pin']);
    Route::delete('/rooms/{room}/unpin/{message}', [PinnedMessageController::class, 'unpin']);

    // ─── Real-time Presence & Read Receipts ───────────────────────────
    Route::post('/rooms/{room}/read', [MessageController::class, 'markAsRead']);
    Route::post('/rooms/{room}/typing', [MessageController::class, 'typing']);
    Route::get('/messages/{message}/read-receipts', [MessageController::class, 'readReceipts']);

    // ─── Messaging Lifecycle & Forwarding ─────────────────────────────
    Route::get('/rooms/{room}/messages', [MessageController::class, 'index']);
    Route::post('/messages', [MessageController::class, 'send']);
    Route::post('/messages/forward', [MessageController::class, 'forward']);
    Route::patch('/messages/{message}', [MessageController::class, 'update']);
    Route::delete('/messages/{message}', [MessageController::class, 'destroy']);

    // ─── Message Reactions (Emoji ❤️, 👍, 😂, 🔥, 😮) ────────────────
    Route::get('/messages/{message}/reactions', [MessageReactionController::class, 'index']);
    Route::post('/messages/{message}/reactions', [MessageReactionController::class, 'toggle']);

    // ─── Chat Settings & Telegram-Style Mute Presets ──────────────────
    Route::patch('/rooms/{room}/settings/notification', [ChatSettingController::class, 'updateNotification']);
    Route::patch('/rooms/{room}/settings/sound', [ChatSettingController::class, 'updateSound']);
    Route::match(['post', 'patch'], '/rooms/{room}/settings/mute', [ChatSettingController::class, 'mute']);
    Route::delete('/rooms/{room}/settings/mute', [ChatSettingController::class, 'unmute']);

    // ─── Block & Privacy ──────────────────────────────────────────────
    Route::post('/block/{user}', [BlockController::class, 'block']);
    Route::delete('/unblock/{user}', [BlockController::class, 'unblock']);
    Route::get('/blocked-users', [BlockController::class, 'blockedUsers']);
});
