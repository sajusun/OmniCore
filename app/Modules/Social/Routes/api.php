<?php

use App\Modules\Social\Http\Controllers\Api\BlockApiController;
use App\Modules\Social\Http\Controllers\Api\FollowApiController;
use App\Modules\Social\Http\Controllers\Api\FriendApiController;
use App\Modules\Social\Http\Controllers\Api\RelationshipApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Social Graph & Network API Routes (Protected via auth:api)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:api')->group(function () {
    // ─── Friendship Lifecycle ─────────────────────────────────────────
    Route::prefix('friends')->group(function () {
        Route::get('/', [FriendApiController::class, 'friends']);
        Route::get('/suggestions', [FriendApiController::class, 'suggestions']);
        Route::get('/requests/pending', [FriendApiController::class, 'pendingRequests']);
        Route::get('/requests/sent', [FriendApiController::class, 'sentRequests']);
        Route::get('/{user}/mutual', [FriendApiController::class, 'mutualFriends']);

        Route::post('/request/{user}', [FriendApiController::class, 'sendRequest']);
        Route::post('/accept/{friendRequest}', [FriendApiController::class, 'accept']);
        Route::post('/reject/{friendRequest}', [FriendApiController::class, 'reject']);
        Route::delete('/cancel/{friendRequest}', [FriendApiController::class, 'cancel']);
        Route::delete('/unfriend/{user}', [FriendApiController::class, 'unfriend']);
    });

    // ─── Follower Network & Relationships ─────────────────────────────
    Route::prefix('users')->group(function () {
        Route::get('/followers', [FollowApiController::class, 'followers']);
        Route::get('/followings', [FollowApiController::class, 'followings']);
        Route::get('/{user}/followers', [FollowApiController::class, 'userFollowers']);
        Route::get('/{user}/followings', [FollowApiController::class, 'userFollowings']);
        Route::get('/{user}/relationship', [RelationshipApiController::class, 'show']);

        Route::post('/{user}/follow', [FollowApiController::class, 'follow']);
        Route::delete('/{user}/unfollow', [FollowApiController::class, 'unfollow']);
        Route::post('/{user}/toggle-follow', [FollowApiController::class, 'toggle']);
    });

    // ─── Block & Privacy Management ───────────────────────────────────
    Route::prefix('blocks')->group(function () {
        Route::get('/', [BlockApiController::class, 'index']);
        Route::post('/{user}', [BlockApiController::class, 'block']);
        Route::delete('/{user}', [BlockApiController::class, 'unblock']);
    });
});
