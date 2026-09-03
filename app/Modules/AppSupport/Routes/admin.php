<?php

use App\Modules\AppSupport\Http\Controllers\Backend\AppSupportBackendController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| App Support Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware('permission:app.support.list|app.support.reply|app.support.status|ticket.list')
    ->prefix('app-supports')
    ->name('app-supports.')
    ->group(function () {
        Route::get('/', [AppSupportBackendController::class, 'index'])->name('index');
        Route::get('/{id}', [AppSupportBackendController::class, 'show'])->name('show');
        Route::post('/{id}/reply', [AppSupportBackendController::class, 'reply'])->name('reply');
        Route::patch('/{id}/status', [AppSupportBackendController::class, 'updateStatus'])->name('status');
    });
