<?php

use App\Modules\Interaction\Http\Controllers\Api\CommentApiController;
use App\Modules\Interaction\Http\Controllers\Api\LikeApiController;
use App\Modules\Interaction\Http\Controllers\Api\ShareApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Interaction API Routes (Protected via auth:api)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:api')->prefix('interactions')->group(function () {
    // ─── Comments & Replies ──────────────────────────────────────────
    Route::prefix('comments')->group(function () {
        Route::get('/', [CommentApiController::class, 'index']);
        Route::post('/', [CommentApiController::class, 'store']);
        Route::delete('/{comment}', [CommentApiController::class, 'destroy']);
    });

    // ─── Likes & Reactions ───────────────────────────────────────────
    Route::prefix('likes')->group(function () {
        Route::post('/toggle', [LikeApiController::class, 'toggle']);
        Route::get('/', [LikeApiController::class, 'likers']);
    });

    // ─── Share Links ─────────────────────────────────────────────────
    Route::prefix('shares')->group(function () {
        Route::post('/', [ShareApiController::class, 'store']);
    });
});
