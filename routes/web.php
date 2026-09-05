<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\NotificationController;
use App\Modules\Auth\Http\Controllers\Api\SocialLoginApiController;


Route::get('/', function () {
    return redirect()->route('login');
})->name('home');


//Social login test routes
Route::get('social-login/{provider}', [SocialLoginApiController::class, 'redirectToProvider'])->name('social.login');
Route::get('social-login/{provider}/callback', [SocialLoginApiController::class, 'handleProviderCallback']);


Route::controller(NotificationController::class)->prefix('notification')->name('notification.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('read/single/{id}', 'readSingle')->name('read.single');
    Route::POST('read/all', 'readAll')->name('read.all');
})->middleware('auth');



Route::get('/broadcast-test', function () {
    return view('broadcast-test');
});

