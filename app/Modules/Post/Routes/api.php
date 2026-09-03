<?php

use App\Modules\Post\Http\Controllers\Api\PostApiController;
use App\Modules\Post\Http\Controllers\Api\PostCommentApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Post API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:api')->group(function () {
    // ─── Post Endpoints ───────────────────────────────────────────
    Route::prefix('posts')->group(function () {
        Route::get('/feeds', [PostApiController::class, 'feed']);
        Route::get('/saved-posts', [PostApiController::class, 'savedPosts']);
        Route::get('/share/{share_link}', [PostApiController::class, 'share'])->name('post.share');

        Route::get('/', [PostApiController::class, 'index']);
        Route::post('/store', [PostApiController::class, 'store']);
        Route::get('/{post}/show', [PostApiController::class, 'show']);
        Route::post('/{post}/update', [PostApiController::class, 'update']);
        Route::delete('/{post}/delete', [PostApiController::class, 'destroy']);

        // Interactions
        Route::post('/{post}/like', [PostApiController::class, 'like']);
        Route::get('/{post}/liked', [PostApiController::class, 'likedUser']);
        Route::post('/{post}/repost', [PostApiController::class, 'repost']);
        Route::post('/{post}/save', [PostApiController::class, 'toggleSave']);

        // Comments under post
        Route::get('/{post}/comments', [PostCommentApiController::class, 'index']);
        Route::post('/{post}/comments', [PostCommentApiController::class, 'store']);
    });

    // ─── Comment Direct Endpoints ─────────────────────────────────
    Route::prefix('comments')->group(function () {
        Route::post('/{comment}/update', [PostCommentApiController::class, 'update']);
        Route::delete('/{comment}/delete', [PostCommentApiController::class, 'destroy']);
        Route::post('/{comment}/reply', [PostCommentApiController::class, 'reply']);
        Route::post('/{comment}/like', [PostCommentApiController::class, 'toggleLike']);
        Route::get('/{comment}/replies', [PostCommentApiController::class, 'replies']);
    });
});
