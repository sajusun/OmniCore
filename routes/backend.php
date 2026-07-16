<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Backend\MediaController;
use App\Http\Controllers\Web\Backend\ContactUsController;

/*
|--------------------------------------------------------------------------
| Backend Routes — protected by 'admin' middleware (applied at app.php level)
|--------------------------------------------------------------------------
*/

// ─── Media Management ─────────────────────────────────────────────────────────
Route::middleware('permission:media.manage')
    ->prefix('media')->name('media.')
    ->group(function () {
        Route::post('/media/{id}/status',   [MediaController::class, 'updateStatus'])->name('status.update');
        Route::delete('/media/{id}/delete', [MediaController::class, 'mediaDelete'])->name('delete');
        Route::post('/media/update-order',  [MediaController::class, 'updateOrder'])->name('order.update');
    });

// ─── Contact Us / Support Center ─────────────────────────────────────────────
Route::middleware('permission:support.center.manage')
    ->group(function () {
        Route::get('/person/contact-me',             [ContactUsController::class, 'index'])->name('contact.me');
        Route::get('/person/contact-me/{id}',        [ContactUsController::class, 'show'])->name('contact.me.show');
        Route::delete('/person/contact-me/delete/{id}', [ContactUsController::class, 'destroy'])->name('contact.me.delete');
        Route::post('/person/contact-me/{id}/reply', [ContactUsController::class, 'reply'])->name('contact-us.reply');
    });
