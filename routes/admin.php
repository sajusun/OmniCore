<?php

use App\Http\Controllers\Web\Backend\Access\AdminController;
use App\Http\Controllers\Web\Backend\Access\PermissionController;
use App\Http\Controllers\Web\Backend\Access\RoleController;
use App\Http\Controllers\Web\Backend\Access\UserController;
use App\Http\Controllers\Web\Backend\DashboardController;
use App\Http\Controllers\Web\Backend\NotificationController;
use App\Http\Controllers\Web\Backend\Pages\PageContentController;
use App\Http\Controllers\Web\Backend\Settings\ProfileController;
use App\Http\Controllers\Web\Backend\Settings\SettingController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes — protected by 'admin' middleware (applied at app.php level)
| Fine-grained permission guards via 'permission' middleware.
| Super Admin automatically bypasses all permission checks.
|--------------------------------------------------------------------------
*/

// ─── Dashboard ────────────────────────────────────────────────────────────────
Route::middleware('permission:dashboard.access')->get('dashboard', [DashboardController::class, 'index'])->name('dashboard');




// ─── User Management (Regular Users) ─────────────────────────────────────────
Route::prefix('users')->name('users.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->middleware('permission:user.list')->name('index');
    Route::get('/create', [UserController::class, 'create'])->middleware('permission:user.create')->name('create');
    Route::post('/store', [UserController::class, 'store'])->middleware('permission:user.create')->name('store');
    Route::get('/{user}', [UserController::class, 'show'])->middleware('permission:user.list')->name('show');
    Route::get('/{user}/edit', [UserController::class, 'edit'])->middleware('permission:user.edit')->name('edit');
    Route::put('/{user}/update', [UserController::class, 'update'])->middleware('permission:user.edit')->name('update');
    Route::delete('/{user}/destroy', [UserController::class, 'destroy'])->middleware('permission:user.delete')->name('destroy');
    Route::get('/status/{id}', [UserController::class, 'status'])->middleware('permission:user.status')->name('status');
});

// ─── Staff / Admin Management ─────────────────────────────────────────────────
Route::prefix('group/stuff')->name('stuff.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->middleware('permission:staff.list')->name('index');
    Route::get('/create', [AdminController::class, 'create'])->middleware('permission:staff.create')->name('create');
    Route::post('/store', [AdminController::class, 'store'])->middleware('permission:staff.create')->name('store');
    Route::get('/{admin}/edit', [AdminController::class, 'edit'])->middleware('permission:staff.edit')->name('edit');
    Route::put('/{admin}/update', [AdminController::class, 'update'])->middleware('permission:staff.edit')->name('update');
    Route::delete('/{admin}/destroy', [AdminController::class, 'destroy'])->middleware('permission:staff.delete')->name('destroy');
});

// ─── Role Management ──────────────────────────────────────────────────────────
Route::middleware('permission:role.list|role.create|role.edit|role.delete')->group(function () {
    Route::get('roles', [RoleController::class, 'index'])->middleware('permission:role.list')->name('roles.index');
    Route::get('roles/create', [RoleController::class, 'create'])->middleware('permission:role.create')->name('roles.create');
    Route::post('roles', [RoleController::class, 'store'])->middleware('permission:role.create')->name('roles.store');
    Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->middleware('permission:role.edit')->name('roles.edit');
    Route::put('roles/{role}', [RoleController::class, 'update'])->middleware('permission:role.edit')->name('roles.update');
    Route::delete('roles/{role}', [RoleController::class, 'destroy'])->middleware('permission:role.delete')->name('roles.destroy');
});

// ─── Permission Management ────────────────────────────────────────────────────
Route::middleware('permission:permission.list|permission.create|permission.edit|permission.delete')->group(function () {
    Route::get('permissions', [PermissionController::class, 'index'])->middleware('permission:permission.list')->name('permissions.index');
    Route::get('permissions/create', [PermissionController::class, 'create'])->middleware('permission:permission.create')->name('permissions.create');
    Route::post('permissions', [PermissionController::class, 'store'])->middleware('permission:permission.create')->name('permissions.store');
    Route::get('permissions/{permission}/edit', [PermissionController::class, 'edit'])->middleware('permission:permission.edit')->name('permissions.edit');
    Route::put('permissions/{permission}', [PermissionController::class, 'update'])->middleware('permission:permission.edit')->name('permissions.update');
    Route::delete('permissions/{permission}', [PermissionController::class, 'destroy'])->middleware('permission:permission.delete')->name('permissions.destroy');
});

// ─── Profile Settings (all authenticated admins) ─────────────────────────────
Route::prefix('setting/profile')->name('setting.profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');
    Route::put('/update', [ProfileController::class, 'UpdateProfile'])->name('update');
    Route::put('/update/password', [ProfileController::class, 'UpdatePassword'])->name('update.password');
    Route::post('/update/avatar', [ProfileController::class, 'UpdateProfilePicture'])->name('avatar.update');
});

// ─── General Settings ─────────────────────────────────────────────────────────
Route::middleware('permission:settings.access')->prefix('setting/general')->name('setting.general.')->group(function () {
    Route::get('/', [SettingController::class, 'index'])->name('index');
    Route::get('/logo', [SettingController::class, 'viewLogo'])->name('logo');
    Route::patch('/logo', [SettingController::class, 'updateLogo'])->middleware('permission:settings.edit')->name('logo.update');
    Route::patch('/update', [SettingController::class, 'update'])->middleware('permission:settings.edit')->name('update');
    // ENV Settings
    Route::get('/env', [SettingController::class, 'env'])->middleware('permission:settings.edit')->name('env');
    Route::post('/env/app', [SettingController::class, 'updateEnvApp'])->middleware('permission:settings.edit')->name('env.app.update');
    Route::post('/env/jwt', [SettingController::class, 'updateEnvJwt'])->middleware('permission:settings.edit')->name('env.jwt.update');
    Route::post('/env/firebase', [SettingController::class, 'updateEnvFirebase'])->middleware('permission:settings.edit')->name('env.firebase.update');
    Route::post('/env/verification', [SettingController::class, 'updateEnvVerification'])->middleware('permission:settings.edit')->name('env.verification.update');
    Route::post('/env/system', [SettingController::class, 'updateEnvSystem'])->middleware('permission:settings.edit')->name('env.system.update');

    // Mail Settings
    Route::get('/mail', [SettingController::class, 'mail'])->name('mail');
    Route::post('/env/mail', [SettingController::class, 'updateMail'])->middleware('permission:settings.edit')->name('env.mail.update');
    Route::post('/mail/send', [SettingController::class, 'sendMail'])->name('mail.send');
});

// ─── System Optimization ──────────────────────────────────────────────────────
Route::middleware('permission:system.optimize')->get('/optimize', function () {
    Artisan::call('optimize:clear');
    Artisan::call('config:cache');
    Cache::flush();

    return redirect()->back()->with('t-success', 'System Optimized Successfully');
})->name('optimize');







