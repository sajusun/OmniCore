<?php

use App\Modules\Subscription\Http\Controllers\Backend\PlanAdminController;
use App\Modules\Subscription\Http\Controllers\Backend\SubscriptionAdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Subscription & Plan Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('plans')->name('plans.')->group(function () {
    Route::get('/', [PlanAdminController::class, 'index'])->name('index');
    Route::get('/create', [PlanAdminController::class, 'create'])->name('create');
    Route::post('/', [PlanAdminController::class, 'store'])->name('store');
    Route::get('/{plan}/edit', [PlanAdminController::class, 'edit'])->name('edit');
    Route::put('/{plan}', [PlanAdminController::class, 'update'])->name('update');
    Route::delete('/{plan}', [PlanAdminController::class, 'destroy'])->name('destroy');
});

Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
    Route::get('/', [SubscriptionAdminController::class, 'index'])->name('index');
    Route::get('/{subscription}', [SubscriptionAdminController::class, 'show'])->name('show');
    Route::post('/grant', [SubscriptionAdminController::class, 'grant'])->name('grant');
    Route::post('/{subscription}/cancel', [SubscriptionAdminController::class, 'cancel'])->name('cancel');
});
