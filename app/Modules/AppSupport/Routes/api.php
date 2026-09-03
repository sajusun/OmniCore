<?php

use App\Modules\AppSupport\Http\Controllers\Api\AppSupportApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| App Support API Routes (Mobile App Endpoints)
|--------------------------------------------------------------------------
*/

// Public / Authenticated helper to get category dropdown list
Route::get('app-support/categories', [AppSupportApiController::class, 'categories']);

// Protected routes for mobile app user
Route::middleware('auth:api')->prefix('app-support')->group(function () {
    Route::post('/', [AppSupportApiController::class, 'store']); // Submit issue/report
    Route::get('/my-reports', [AppSupportApiController::class, 'index']); // List user's reports
    Route::get('/{id}', [AppSupportApiController::class, 'show']); // View specific report details
});
