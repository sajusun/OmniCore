<?php

use App\Modules\Notification\Http\Controllers\Api\FirebaseTokenApiController;
use App\Modules\Notification\Http\Controllers\Api\NotificationApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Notification & Device Session API Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:api', 'throttle:api'])->group(function () {
    // ─── Firebase Tokens & Device Sessions ───────────────────────────────
    Route::prefix('firebase/tokens')->controller(FirebaseTokenApiController::class)->name('firebase.tokens.')->group(function () {
        Route::get('/', 'index')->name('index');                         // List all logged-in devices
        Route::post('/', 'store')->name('store');                        // Save/Update FCM token & session
        Route::delete('/others', 'revokeOthers')->name('revoke_others'); // Logout/Revoke all other devices
        Route::delete('/all', 'revokeAll')->name('revoke_all');          // Logout/Revoke all devices
        Route::delete('/{deviceId?}', 'destroy')->name('destroy');       // Revoke specific device session
        Route::post('/touch', 'touch')->name('touch');                   // Refresh activity
    });

    // ─── Legacy Firebase Aliases (for backward compatibility) ────────────
    Route::prefix('firebase')->controller(FirebaseTokenApiController::class)->group(function () {
        Route::post('firebase-token', 'store');
        Route::post('firebase-token/delete', 'destroy');
        Route::post('firebase-token/touch', 'touch');
    });

    // ─── In-App Notifications ─────────────────────────────────────────────
    Route::prefix('notifications')->controller(NotificationApiController::class)->name('notifications.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/unread-count', 'unreadCount')->name('unread_count');
        Route::post('/{notification}/read', 'markAsRead')->name('read');
        Route::post('/read-all', 'markAllAsRead')->name('read_all');
        Route::delete('/destroy-all', 'destroyAll')->name('destroy_all');
        Route::delete('/{notification}', 'destroy')->name('destroy');
    });
});
