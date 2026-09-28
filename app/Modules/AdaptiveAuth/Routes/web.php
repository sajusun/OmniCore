<?php

declare(strict_types=1);

use App\Modules\AdaptiveAuth\Http\Controllers\Web\AdaptiveAuthWebController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->prefix('adaptive-auth')->name('adaptive.')->group(function () {
    // Guest/Auth Challenge Flow
    Route::get('/challenge', [AdaptiveAuthWebController::class, 'showChallenge'])->name('challenge');
    Route::post('/verify', [AdaptiveAuthWebController::class, 'verify'])->name('challenge.verify');
    Route::post('/resend', [AdaptiveAuthWebController::class, 'resend'])->name('challenge.resend');

    // Authenticated Device Management Flow
    Route::middleware(['auth'])->group(function () {
        Route::get('/devices', [AdaptiveAuthWebController::class, 'devices'])->name('devices.index');
        Route::post('/devices/{id}/revoke', [AdaptiveAuthWebController::class, 'revoke'])->name('devices.revoke');
        Route::post('/devices/revoke-others', [AdaptiveAuthWebController::class, 'revokeOthers'])->name('devices.revoke_others');
        Route::post('/audit-logs/clear', [AdaptiveAuthWebController::class, 'clearAuditLogs'])->name('devices.clear_logs');
    });
});


