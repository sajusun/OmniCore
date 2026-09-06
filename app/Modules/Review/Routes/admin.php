<?php

use App\Modules\Review\Controllers\Admin\ReviewAdminController;
use Illuminate\Support\Facades\Route;

Route::middleware('permission:review.list|review.moderate|review.delete')->prefix('reviews')->name('reviews.')->group(function () {
    Route::get('/', [ReviewAdminController::class, 'index'])->name('index');
    Route::patch('/{review}/moderate', [ReviewAdminController::class, 'moderate'])->name('moderate');
    Route::post('/{review}/reply', [ReviewAdminController::class, 'reply'])->name('reply');
    Route::delete('/{review}', [ReviewAdminController::class, 'destroy'])->name('destroy');
});
