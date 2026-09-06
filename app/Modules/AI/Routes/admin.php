<?php

use App\Modules\AI\Http\Controllers\Admin\AiAdminController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'verified'])->prefix('admin/ai')->name('admin.ai.')->group(function () {
    Route::get('/knowledge-base', [AiAdminController::class, 'index'])->name('knowledge-base.index');
    Route::post('/knowledge-base', [AiAdminController::class, 'store'])->name('knowledge-base.store');
    Route::put('/knowledge-base/{id}', [AiAdminController::class, 'update'])->name('knowledge-base.update');
    Route::delete('/knowledge-base/{id}', [AiAdminController::class, 'destroy'])->name('knowledge-base.destroy');
});
