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
Route::middleware(['throttle:10,1'])->group(function () {
    Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
    Route::post('/contact-us', [ContactController::class, 'store'])->name('contact.store');
});

Route::post('app/webhooks/revenuecat', RevenueCatWebhookController::class)->name('webhooks.revenuecat');

/*
|--------------------------------------------------------------------------
| Protected Root Service Endpoints
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:api', 'throttle:api'])->group(function () {
    // ─── Firebase Tokens & Device Sessions ───────────────────────────────
    Route::prefix('firebase/tokens')->controller(FirebaseTokenController::class)->name('firebase.tokens.')->group(function () {
        Route::get('/', 'index')->name('index');                         // List all logged-in devices
        Route::post('/', 'store')->name('store');                        // Save/Update FCM token & session
        Route::delete('/others', 'revokeOthers')->name('revoke_others'); // Logout/Revoke all other devices
        Route::delete('/all', 'revokeAll')->name('revoke_all');          // Logout/Revoke all devices
        Route::delete('/{deviceId?}', 'destroy')->name('destroy');       // Revoke specific device session
        Route::post('/touch', 'touch')->name('touch');                   // Refresh activity
    });

    // ─── Legacy Firebase Aliases (for backward compatibility) ────────────
    Route::prefix('firebase')->controller(FirebaseTokenController::class)->group(function () {
        Route::post('firebase-token', 'store');
        Route::post('firebase-token/delete', 'destroy');
        Route::post('firebase-token/touch', 'touch');
    });

    // ─── Notifications ───────────────────────────────────────────────────
    Route::prefix('notifications')->controller(NotificationController::class)->name('notifications.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/unread-count', 'unreadCount')->name('unread_count');
        Route::post('/{notification}/read', 'markAsRead')->name('read');
        Route::post('/read-all', 'markAllAsRead')->name('read_all');
        Route::delete('/destroy-all', 'destroyAll')->name('destroy_all');
        Route::delete('/{notification}', 'destroy')->name('destroy');
    });
});
