<?php

use App\Modules\Subscription\Http\Controllers\Api\PlanApiController;
use App\Modules\Subscription\Http\Controllers\Api\SubscriptionApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Subscription & Plans API Routes
|--------------------------------------------------------------------------
*/

// Public Plans
Route::prefix('plans')->group(function () {
    Route::get('/', [PlanApiController::class, 'index']);
    Route::get('{slug}', [PlanApiController::class, 'show']);
});

// Authenticated User Subscriptions
Route::middleware('auth:api')->prefix('subscriptions')->group(function () {
    Route::get('current', [SubscriptionApiController::class, 'current']);
    Route::post('subscribe', [SubscriptionApiController::class, 'subscribe']);
    Route::post('cancel', [SubscriptionApiController::class, 'cancel']);
    Route::post('resume', [SubscriptionApiController::class, 'resume']);
    Route::get('features/{featureCode}', [SubscriptionApiController::class, 'checkFeature']);
});
