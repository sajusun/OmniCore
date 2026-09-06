<?php

use App\Modules\Review\Controllers\Api\ReviewApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/reviews')->group(function () {
    // Public endpoints
    Route::get('/', [ReviewApiController::class, 'index'])->name('api.reviews.index');
    Route::get('/summary', [ReviewApiController::class, 'summary'])->name('api.reviews.summary');

    // Authenticated endpoints
    Route::middleware(['auth:api'])->group(function () {
        Route::post('/', [ReviewApiController::class, 'store'])->name('api.reviews.store');
        Route::post('/{review}/vote', [ReviewApiController::class, 'vote'])->name('api.reviews.vote');
        Route::delete('/{review}', [ReviewApiController::class, 'destroy'])->name('api.reviews.destroy');
    });
});
