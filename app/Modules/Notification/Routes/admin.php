<?php

use App\Modules\Notification\Http\Controllers\Backend\BulkNotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Bulk Notification & Custom Mail Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware('permission:notification.send')->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [BulkNotificationController::class, 'index'])->name('index');
    Route::get('/create', [BulkNotificationController::class, 'index'])->name('create');
    Route::post('/send-inapp', [BulkNotificationController::class, 'sendInApp'])->name('send-inapp');
    Route::post('/send-email', [BulkNotificationController::class, 'sendEmail'])->name('send-email');
    Route::post('/store', [BulkNotificationController::class, 'sendInApp'])->name('store');
});
