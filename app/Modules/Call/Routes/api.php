<?php

use App\Modules\Call\Http\Controllers\Api\CallLogApiController;
use App\Modules\Call\Http\Controllers\Api\CallSignalingApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Audio/Video Call & Screen Sharing API Routes (Protected via auth:api)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:api')->prefix('calls')->group(function () {
    // ─── Call Signaling & State Engine ────────────────────────────────
    Route::post('/initiate', [CallSignalingApiController::class, 'initiate']);
    Route::post('/{callSession}/ring', [CallSignalingApiController::class, 'ring']);
    Route::post('/{callSession}/accept', [CallSignalingApiController::class, 'accept']);
    Route::post('/{callSession}/reject', [CallSignalingApiController::class, 'reject']);
    Route::post('/{callSession}/end', [CallSignalingApiController::class, 'end']);
    Route::post('/{callSession}/busy', [CallSignalingApiController::class, 'busy']);
    Route::post('/{callSession}/signal', [CallSignalingApiController::class, 'signal']);
    Route::post('/{callSession}/tracks', [CallSignalingApiController::class, 'trackState']);

    // ─── Call Logs & History ──────────────────────────────────────────
    Route::get('/history', [CallLogApiController::class, 'history']);
    Route::get('/missed', [CallLogApiController::class, 'missed']);
    Route::get('/{callSession}', [CallLogApiController::class, 'show']);
    Route::delete('/{callSession}', [CallLogApiController::class, 'destroy']);
});
