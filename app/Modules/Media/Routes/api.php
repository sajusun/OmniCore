<?php

use App\Modules\Media\Controllers\MediaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Media Module API Routes (Protected via auth:api)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:api')->prefix('media')->group(function () {
    Route::get('/', [MediaController::class, 'index']);
    Route::get('/{id}', [MediaController::class, 'show']);
    Route::delete('/{media}', [MediaController::class, 'deleteSingle']);
    Route::delete('/', [MediaController::class, 'destroy']);
    Route::post('/{media}/primary', [MediaController::class, 'setPrimary']);
    Route::post('/sort-order', [MediaController::class, 'sortOrder']);
});