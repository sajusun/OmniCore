<?php

use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\FirebaseTokenController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\Webhooks\RevenueCatWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public General Root Endpoints
|--------------------------------------------------------------------------
*/
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe']);
Route::post('/contact-us', [ContactController::class, 'store']);
Route::post('app/webhooks/revenuecat', RevenueCatWebhookController::class);

/*
|--------------------------------------------------------------------------
| Protected Root Service Endpoints
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:api', 'throttle:api'])->group(function () {
    // ─── Firebase Tokens ─────────────────────────────────────────────────
    Route::prefix('firebase')->controller(FirebaseTokenController::class)->group(function () {
        Route::post('firebase-token', 'store');
        Route::post('firebase-token/delete', 'destroy');
        Route::post('firebase-token/touch', 'touch');
    });

    // ─── Notifications ───────────────────────────────────────────────────
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/destroy-all', [NotificationController::class, 'destroyAll']);
        Route::delete('/delete-all', [NotificationController::class, 'destroyAll']);
        Route::delete('/{notification}', [NotificationController::class, 'destroy']);
    });
});

