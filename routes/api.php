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

