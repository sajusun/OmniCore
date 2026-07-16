<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Web\Backend\PageController;
use App\Http\Controllers\Web\Backend\FoodLogController;
use App\Http\Controllers\Web\Backend\DashboardController;
use App\Http\Controllers\Web\Backend\Access\RoleController;
use App\Http\Controllers\Web\Backend\Access\UserController;
use App\Http\Controllers\Web\Backend\ScanHistoryController;
use App\Http\Controllers\Web\Backend\Access\AdminController;
use App\Http\Controllers\Web\Backend\Settings\ProfileController;
use App\Http\Controllers\Web\Backend\Settings\SettingController;
use App\Http\Controllers\Web\Backend\SubscriptionPlanController;
use App\Http\Controllers\Web\Backend\Access\PermissionController;
use App\Http\Controllers\Web\Backend\SubscriptionPlanItemController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

// Dashboard
Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('data', [DashboardController::class, 'data'])->name('data');

// Scan History
Route::group(['prefix' => 'scan-histories', 'as' => 'scan_histories.'], function () {
    Route::get('/', [ScanHistoryController::class, 'index'])->name('index');
    Route::get('/ajax', [ScanHistoryController::class, 'ajax'])->name('ajax');
    Route::get('/{scanHistory}', [ScanHistoryController::class, 'show'])->name('show');
    Route::delete('/{scanHistory}/delete', [ScanHistoryController::class, 'delete'])->name('delete');
});

// Food Logs
Route::group(['prefix' => 'food-logs', 'as' => 'food_logs.'], function () {
    Route::get('/', [FoodLogController::class, 'index'])->name('index');
    Route::get('/ajax', [FoodLogController::class, 'ajax'])->name('ajax');
    Route::get('/{foodLog}', [FoodLogController::class, 'show'])->name('show');
    Route::delete('/{foodLog}/delete', [FoodLogController::class, 'delete'])->name('delete');
});

// User Management (Regular Users)
Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/create', [UserController::class, 'create'])->name('create');
    Route::post('/store', [UserController::class, 'store'])->name('store');
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
    Route::get('/{user}/show', [UserController::class, 'show'])->name('show');
    Route::put('/{user}/update', [UserController::class, 'update'])->name('update');
    Route::delete('/{user}/destroy', [UserController::class, 'destroy'])->name('destroy');
    Route::get('/status/{id}', [UserController::class, 'status'])->name('status');
});

// Admin Management (Staff)
Route::group(['prefix' => 'admins', 'as' => 'admins.'], function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::get('/create', [AdminController::class, 'create'])->name('create');
    Route::post('/store', [AdminController::class, 'store'])->name('store');
    Route::get('/{admin}/edit', [AdminController::class, 'edit'])->name('edit');
    Route::put('/{admin}/update', [AdminController::class, 'update'])->name('update');
    Route::delete('/{admin}/destroy', [AdminController::class, 'destroy'])->name('destroy');
});

// Role & Permission Management
Route::resource('roles', RoleController::class);
Route::resource('permissions', PermissionController::class);

// Profile Settings
Route::group(['prefix' => 'setting/profile', 'as' => 'setting.profile.'], function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');
    Route::put('/update', [ProfileController::class, 'UpdateProfile'])->name('update');
    Route::put('/update/password', [ProfileController::class, 'UpdatePassword'])->name('update.password');
});
Route::post('setting/profile/update/picture', [ProfileController::class, 'UpdateProfilePicture'])->name('update.profile.picture');

// General Settings
Route::group(['prefix' => 'setting/general', 'as' => 'setting.general.'], function () {
    Route::get('/', [SettingController::class, 'index'])->name('index');
    Route::patch('/update', [SettingController::class, 'update'])->name('update');
});

// Subscription Plans
Route::group(['prefix' => 'subscription-plans', 'as' => 'subscription_plans.'], function () {
    Route::get('/', [SubscriptionPlanController::class, 'index'])->name('index');
    Route::get('/create', [SubscriptionPlanController::class, 'create'])->name('create');
    Route::post('/store', [SubscriptionPlanController::class, 'store'])->name('store');
    Route::get('/{subscriptionPlan}/edit', [SubscriptionPlanController::class, 'edit'])->name('edit');
    Route::put('/{subscriptionPlan}/update', [SubscriptionPlanController::class, 'update'])->name('update');
    Route::delete('/{subscriptionPlan}/destroy', [SubscriptionPlanController::class, 'destroy'])->name('destroy');
    Route::get('/status/{id}', [SubscriptionPlanController::class, 'status'])->name('status');

    // Items
    Route::get('/{plan}/items', [SubscriptionPlanItemController::class, 'index'])->name('items.index');
    Route::post('/{plan}/items/store', [SubscriptionPlanItemController::class, 'store'])->name('items.store');
    Route::get('/items/{item}/edit', [SubscriptionPlanItemController::class, 'edit'])->name('items.edit');
    Route::put('/items/{item}/update', [SubscriptionPlanItemController::class, 'update'])->name('items.update');
    Route::delete('/items/{item}/destroy', [SubscriptionPlanItemController::class, 'destroy'])->name('items.destroy');
    Route::get('/items/status/{item}', [SubscriptionPlanItemController::class, 'status'])->name('items.status');
});

// single page cms
Route::group(['prefix' => 'cms', 'as' => 'cms.'], function () {
    Route::get('/page/{page}/{section}', [PageController::class, 'index'])->name('privacy.index');
    Route::post('page/{page}/{section}', [PageController::class, 'update'])->name('privacy.update');
});

Route::prefix('admin')->middleware('auth')->group(function () {
    Route::resource('products', ProductController::class);
    Route::get('products-data', [ProductController::class, 'data'])->name('products.data');
});



// System Optimization
Route::get('/optimize', function () {
    Artisan::call('optimize:clear');
    Artisan::call('config:cache');
    Cache::flush();

    return redirect()->back()->with('t-success', 'System Optimized Successfully');
})->name('optimize');
