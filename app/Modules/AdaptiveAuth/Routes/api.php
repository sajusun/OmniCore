<?php

declare(strict_types=1);

use App\Modules\AdaptiveAuth\Http\Controllers\Api\AdaptiveAuthApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('adaptive-auth')->group(function () {
    // ── Public Login Challenge Verification Endpoints ──────────────────────
    Route::post('/verify', [AdaptiveAuthApiController::class, 'verify'])->name('api.adaptive.verify');
    Route::post('/verify-totp', [AdaptiveAuthApiController::class, 'verifyTotp'])->name('api.adaptive.verify_totp');
    Route::post('/resend', [AdaptiveAuthApiController::class, 'resend'])->name('api.adaptive.resend');

    // ── Authenticated Device & MFA Management Endpoints ────────────────────
    Route::middleware(['auth:sanctum,api'])->group(function () {
        // Device Management
        Route::get('/devices', [AdaptiveAuthApiController::class, 'listDevices'])->name('api.adaptive.devices');
        Route::delete('/devices/others', [AdaptiveAuthApiController::class, 'revokeOtherDevices'])->name('api.adaptive.devices.revoke_others');
        Route::delete('/devices/{id}', [AdaptiveAuthApiController::class, 'revokeDevice'])->name('api.adaptive.devices.revoke');
        Route::delete('/audit-logs', [AdaptiveAuthApiController::class, 'clearAuditLogs'])->name('api.adaptive.audit_logs.clear');

        // TOTP 2FA Management
        Route::post('/totp/setup', [AdaptiveAuthApiController::class, 'totpSetup'])->name('api.adaptive.totp.setup');
        Route::post('/totp/enable', [AdaptiveAuthApiController::class, 'totpEnable'])->name('api.adaptive.totp.enable');
        Route::post('/totp/disable', [AdaptiveAuthApiController::class, 'totpDisable'])->name('api.adaptive.totp.disable');
        Route::post('/totp/preference', [AdaptiveAuthApiController::class, 'updateTotpPreference'])->name('api.adaptive.totp.preference');
        Route::post('/totp/regenerate-recovery-codes', [AdaptiveAuthApiController::class, 'regenerateRecoveryCodes'])->name('api.adaptive.totp.regenerate_recovery_codes');
    });
});
