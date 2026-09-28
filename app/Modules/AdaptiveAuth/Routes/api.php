<?php

declare(strict_types=1);

use App\Modules\AdaptiveAuth\Http\Controllers\Api\AdaptiveAuthApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('adaptive-auth')->group(function () {
    // Public challenge verification endpoints
    Route::post('/verify', [AdaptiveAuthApiController::class, 'verify']);
    Route::post('/resend', [AdaptiveAuthApiController::class, 'resend']);

    // Authenticated device management endpoints
    Route::middleware(['auth:sanctum,api'])->group(function () {
        Route::get('/devices', [AdaptiveAuthApiController::class, 'listDevices']);
        Route::delete('/devices/{id}', [AdaptiveAuthApiController::class, 'revokeDevice']);
    });
});
