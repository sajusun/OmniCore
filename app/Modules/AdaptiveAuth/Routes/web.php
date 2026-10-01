<?php

declare(strict_types=1);

use App\Modules\AdaptiveAuth\Http\Controllers\Web\AdaptiveAuthWebController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->prefix('adaptive-auth')->name('adaptive.')->group(function () {
    // Guest/Auth Challenge Flow (Email OTP)
    Route::get('/challenge', [AdaptiveAuthWebController::class, 'showChallenge'])->name('challenge');
    Route::post('/verify', [AdaptiveAuthWebController::class, 'verify'])->name('challenge.verify');
    Route::post('/resend', [AdaptiveAuthWebController::class, 'resend'])->name('challenge.resend');

    // Guest/Auth Challenge Flow (TOTP Authenticator)
    Route::get('/totp-challenge', [AdaptiveAuthWebController::class, 'showTotpChallenge'])->name('totp.challenge');
    Route::post('/totp-verify', [AdaptiveAuthWebController::class, 'verifyTotpChallenge'])->name('totp.verify');

    // Authenticated Device & Security Management Flow
    Route::middleware(['auth'])->group(function () {
        Route::get('/devices', [AdaptiveAuthWebController::class, 'devices'])->name('devices.index');
        Route::match(['post', 'delete'], '/devices/{id}/revoke', [AdaptiveAuthWebController::class, 'revoke'])->name('devices.revoke');
        Route::match(['post', 'delete'], '/devices/revoke-others', [AdaptiveAuthWebController::class, 'revokeOthers'])->name('devices.revoke_others');
        Route::match(['post', 'delete'], '/audit-logs/clear', [AdaptiveAuthWebController::class, 'clearAuditLogs'])->name('devices.clear_logs');

        // TOTP Authenticator Management
        Route::get('/totp/setup', [AdaptiveAuthWebController::class, 'showTotpSetup'])->name('totp.setup');
        Route::post('/totp/enable', [AdaptiveAuthWebController::class, 'enableTotp'])->name('totp.enable');
        Route::post('/totp/disable', [AdaptiveAuthWebController::class, 'disableTotp'])->name('totp.disable');
        Route::post('/totp/preference', [AdaptiveAuthWebController::class, 'updateTotpPreference'])->name('totp.preference');

        // Step-Up Re-Authentication for Sensitive Actions
        Route::post('/step-up/confirm', [AdaptiveAuthWebController::class, 'confirmStepUp'])->name('step_up.confirm');
    });
});
