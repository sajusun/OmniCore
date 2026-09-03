<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Web\Backend\TicketController;
use App\Http\Controllers\Web\Backend\DashboardController;
use App\Http\Controllers\Web\Backend\Access\RoleController;
use App\Http\Controllers\Web\Backend\Access\UserController;
use App\Http\Controllers\Web\Backend\Event\EventController;
use App\Http\Controllers\Web\Backend\Post\PostController;
use App\Http\Controllers\Web\Backend\Club\ClubController;
use App\Http\Controllers\Web\Backend\Vehicle\VehicleController;
use App\Http\Controllers\Web\Backend\Access\AdminController;
use App\Http\Controllers\Web\Backend\NotificationController;
use App\Http\Controllers\Web\Backend\Settings\ProfileController;
use App\Http\Controllers\Web\Backend\Settings\SettingController;
use App\Http\Controllers\Web\Backend\Access\PermissionController;
use App\Http\Controllers\Web\Backend\Pages\PageContentController;

/*
|--------------------------------------------------------------------------
| Admin Routes — protected by 'admin' middleware (applied at app.php level)
| Fine-grained permission guards via 'permission' middleware.
| Super Admin automatically bypasses all permission checks.
|--------------------------------------------------------------------------
*/

// ─── Dashboard ────────────────────────────────────────────────────────────────
Route::middleware('permission:dashboard.access')->get('dashboard', [DashboardController::class, 'index'])->name('dashboard');



// ─── Support Tickets ──────────────────────────────────────────────────────────
Route::middleware('permission:ticket.list|ticket.reply|ticket.close')->prefix('tickets')->name('tickets.')->group(function () {
    Route::get('/', [TicketController::class, 'index'])->name('index');
    Route::get('/{ticket}', [TicketController::class, 'show'])->middleware('permission:ticket.list')->name('show');
    Route::post('/{ticket}/reply', [TicketController::class, 'reply'])->middleware('permission:ticket.reply')->name('reply');
    Route::patch('/{ticket}/close', [TicketController::class, 'close'])->middleware('permission:ticket.close')->name('close');
});

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

Route::prefix('events')->name('events.')->group(function () {
    Route::get('/', [EventController::class, 'index'])->name('index');
    Route::get('/create', [EventController::class, 'create'])->name('create');
    Route::post('/store', [EventController::class, 'store'])->name('store');
    Route::get('/{event}/edit', [EventController::class, 'edit'])->name('edit');
    Route::put('/{event}/update', [EventController::class, 'update'])->name('update');
    Route::delete('/{event}/destroy', [EventController::class, 'destroy'])->name('destroy');
    Route::get('/status/{event_id}', [EventController::class, 'status'])->name('status');
});

Route::prefix('posts')->name('posts.')->group(function () {
    Route::get('/', [PostController::class, 'index'])->name('index');
    Route::get('/{post}', [PostController::class, 'show'])->name('show');
    Route::delete('/{post}/destroy', [PostController::class, 'destroy'])->name('destroy');
});

Route::prefix('clubs')->name('clubs.')->group(function () {
    Route::get('/', [ClubController::class, 'index'])->name('index');
    Route::get('/{club}', [ClubController::class, 'show'])->name('show');
    Route::put('/{club}/status', [ClubController::class, 'updateStatus'])->name('status.update');
    Route::delete('/{club}/destroy', [ClubController::class, 'destroy'])->name('destroy');
});

Route::prefix('vehicles')->name('vehicles.')->group(function () {
    Route::get('/', [VehicleController::class, 'index'])->name('index');
    Route::get('/{vehicle}', [VehicleController::class, 'show'])->name('show');
    Route::delete('/{vehicle}/destroy', [VehicleController::class, 'destroy'])->name('destroy');
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







