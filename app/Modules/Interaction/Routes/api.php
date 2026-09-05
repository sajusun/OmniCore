<?php

use App\Modules\Interaction\Http\Controllers\Api\BookmarkApiController;
use App\Modules\Interaction\Http\Controllers\Api\CommentApiController;
use App\Modules\Interaction\Http\Controllers\Api\LikeApiController;
use App\Modules\Interaction\Http\Controllers\Api\ShareApiController;
use App\Modules\Interaction\Http\Controllers\Api\ViewApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Interaction API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('interactions')->group(function () {
    // ─── Views & Impressions (Supports both Guests & Auth Users) ──────
    Route::prefix('views')->group(function () {
        Route::post('/', [ViewApiController::class, 'store']);
        Route::get('/stats', [ViewApiController::class, 'stats']);
    });

    // ─── Protected Routes (Requires auth:api) ─────────────────────────
    Route::middleware('auth:api')->group(function () {
        // Comments & Replies
        Route::prefix('comments')->group(function () {
            Route::get('/', [CommentApiController::class, 'index']);
            Route::post('/', [CommentApiController::class, 'store']);
            Route::delete('/{comment}', [CommentApiController::class, 'destroy']);
        });

        // Likes & Reactions
        Route::prefix('likes')->group(function () {
            Route::post('/toggle', [LikeApiController::class, 'toggle']);
            Route::get('/', [LikeApiController::class, 'likers']);
        });

        // Share Links
        Route::prefix('shares')->group(function () {
            Route::post('/', [ShareApiController::class, 'store']);
        });

        // Bookmarks / Saved / Wishlists
        Route::prefix('bookmarks')->group(function () {
            Route::get('/', [BookmarkApiController::class, 'index']);
            Route::post('/toggle', [BookmarkApiController::class, 'toggle']);
        });
    });
});
