<?php

use App\Modules\Subscription\Http\Controllers\Admin\SubscriptionAdminController;
use Illuminate\Support\Facades\Route;

Route::prefix('subscription')->name('subscription.')->group(function () {
    Route::get('/plans', [SubscriptionAdminController::class, 'plans'])->name('plans.index');
    Route::post('/plans', [SubscriptionAdminController::class, 'storePlan'])->name('plans.store');
    Route::put('/plans/{id}', [SubscriptionAdminController::class, 'updatePlan'])->name('plans.update');
    Route::delete('/plans/{id}', [SubscriptionAdminController::class, 'deletePlan'])->name('plans.destroy');
    Route::get('/subscribers', [SubscriptionAdminController::class, 'subscriptions'])->name('subscriptions.index');
});
