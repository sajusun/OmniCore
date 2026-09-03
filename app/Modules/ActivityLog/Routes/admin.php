<?php

use App\Modules\ActivityLog\Http\Controllers\ActivityLogController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Activity Log Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware('permission:activity.log.view')->group(function () {
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
    Route::get('users/{user}/activity-logs', [ActivityLogController::class, 'userIndex'])->name('users.activity-logs');
});
