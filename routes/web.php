<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Web\NotificationController;
use App\Http\Controllers\Api\Auth\SocialLoginController;


Route::get('/',
    function () {
        return redirect()->route('login');
    }
)->name('home');

// Route::get('/affiliate/{slug}',[AffiliateController::class, 'store'])->name('store');

// Route::get('/post',[HomeController::class, 'index'])->name('post.index');
// Route::get('/post/show/{slug}',[HomeController::class, 'post'])->name('post.show');

//Social login test routes
Route::get('social-login/{provider}',[SocialLoginController::class,'RedirectToProvider'])->name('social.login');
Route::get('social-login/{provider}/callback',[SocialLoginController::class, 'HandleProviderCallback']);

// Route::post('subscriber/store',[SubscriberController::class, 'store'])->name('subscriber.data.store');

// Route::post('contact/store',[ContactController::class, 'store'])->name('contact.store');

Route::controller(NotificationController::class)->prefix('notification')->name('notification.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('read/single/{id}', 'readSingle')->name('read.single');
    Route::POST('read/all', 'readAll')->name('read.all');
})->middleware('auth');

// Route::get('/page/{slug}',[PageController::class, 'index']);

Route::get('/notification-test', function () {
    return view('notification-test');
});
require __DIR__.'/auth.php';