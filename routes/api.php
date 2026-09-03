<?php

use App\Events\TestBroadcastEvent;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\Auth\SocialLoginController;
use App\Http\Controllers\Api\Auth\UserController;
use App\Http\Controllers\Api\Chat\BlockController;
use App\Http\Controllers\Api\Chat\ChatRoomController;
use App\Http\Controllers\Api\Chat\ChatSettingController;
use App\Http\Controllers\Api\Chat\MessageController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\FirebaseTokenController;
use App\Http\Controllers\Api\Frontend\ClubController;
use App\Http\Controllers\Api\Frontend\CmsController;
use App\Http\Controllers\Api\Frontend\EventController;
use App\Http\Controllers\Api\Frontend\VehicleController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\VerificationController;
use App\Http\Controllers\Api\Webhooks\RevenueCatWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'guest:api'], function ($router) {

    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/register/verify', [RegisterController::class, 'VerifyEmail']);
    Route::post('/otp/resend', [RegisterController::class, 'ResendOtp']);
    Route::post('/otp/verify', [RegisterController::class, 'VerifyEmail']);

    Route::post('login', [LoginController::class, 'login'])->name('api.login');
    Route::post('/forget-password', [ResetPasswordController::class, 'forgotPassword']);
    Route::post('/forget-password/token', [ResetPasswordController::class, 'resetSecretKey']);
    Route::post('/reset-password', [ResetPasswordController::class, 'ResetPassword']);
    // social login
    Route::post('/social-login', [SocialLoginController::class, 'SocialLogin']);
});

Route::group(['middleware' => 'auth:api'], function ($router) {
    Route::get('/refresh-token', [LoginController::class, 'refreshToken']);
    Route::post('/logout', [LogoutController::class, 'logout']);
    Route::get('/me', [UserController::class, 'me']);
    Route::get('/profile/{user}', [UserController::class, 'publicProfile']);
    Route::post('/update-profile', [UserController::class, 'updateProfile']);
    Route::post('/onboarding-fill', [UserController::class, 'onboardingUpdate']);
    Route::post('/update-avatar', [UserController::class, 'updateAvatar']);
    Route::post('/update-password', [UserController::class, 'updatePassword']);
    Route::delete('/profile/delete', [UserController::class, 'destroy']);
});
Route::get('/header-test', function (Request $request) {
    return response()->json([
        'authorization' => $request->header('Authorization'),
        'bearer' => $request->bearerToken(),
    ]);
});

Route::middleware('auth:api')->group(function () {
    Route::get('/vehicles/search', [VehicleController::class, 'search']);
    Route::get('/garages/{garageId}/vehicles', [VehicleController::class, 'getByGarage']);
    Route::delete('/vehicles/media/{media}', [VehicleController::class, 'deleteImage']);

    Route::get('/vehicles', [VehicleController::class, 'index']);
    Route::get('/vehicle-list-for-rsvp', [VehicleController::class, 'miniVehicleData']);
    Route::post('/vehicles/store', [VehicleController::class, 'store']);
    Route::post('/vehicles/{vehicle}/status', [VehicleController::class, 'toggleStatus']);
    Route::get('/vehicles/{vehicle}/show', [VehicleController::class, 'show']);
    Route::post('/vehicles/{vehicle}/update', [VehicleController::class, 'update']);
    Route::post('/vehicles/{vehicle}/delete', [VehicleController::class, 'destroy']);
    Route::get('/vehicles/meta', [VehicleController::class, 'meta']);
});

Route::middleware('auth:api')->group(function () {
    Route::get('/clubs', [ClubController::class, 'index']);
    Route::get('/my-clubs', [ClubController::class, 'myClub']);
    Route::post('/clubs/store', [ClubController::class, 'store']);
    Route::get('/clubs/{id}/show', [ClubController::class, 'show']);
    Route::post('/clubs/{club}/update', [ClubController::class, 'update']);
    Route::delete('/clubs/{club}/delete', [ClubController::class, 'destroy']);

    // ── Membership ──────────────────────────────────────────────────────────
    Route::post('/clubs/{club}/join', [ClubController::class, 'join']);
    Route::delete('/clubs/{club}/leave', [ClubController::class, 'leave']);
    Route::get('/clubs/{club}/members', [ClubController::class, 'members']);

    // Future admin-approval routes (ready to activate when needed)
    Route::post('/clubs/{club}/members/{user}/approve', [ClubController::class, 'approveMember']);
    Route::post('/clubs/{club}/members/{user}/reject', [ClubController::class, 'rejectMember']);
    Route::delete('/clubs/{club}/members/{user}', [ClubController::class, 'removeMember']);
});

Route::middleware('auth:api')->group(function () {
    Route::get('/events/meta', [EventController::class, 'meta']);
    Route::get('/events/bookmarked', [EventController::class, 'bookmarkedEvents']);
    Route::get('/events', [EventController::class, 'index']);
    Route::get('/my-events', [EventController::class, 'myEvent']);
    Route::post('/events/store', [EventController::class, 'store']);
    Route::get('/events/{id}/show', [EventController::class, 'show']);
    Route::post('/events/{event}/update', [EventController::class, 'update']);
    Route::delete('/events/{event}/delete', [EventController::class, 'destroy']);

    // RSVP
    Route::post('/events/{event}/rsvp', [EventController::class, 'rsvp']);
    Route::get('/events/{event}/attendee-list', [EventController::class, 'attendeeList']);
    Route::delete('/events/{event}/rsvp', [EventController::class, 'cancelRsvp']);
    Route::get('/events/{event}/rsvps', [EventController::class, 'rsvps']);

    Route::get('/events/{event}/rsvps/matching', [EventController::class, 'matchingParts']);
    Route::get('/events/{event}/rsvps/all-parts', [EventController::class, 'allParts']);

    // Bookmark
    Route::post('/events/{event}/bookmark', [EventController::class, 'toggleBookmark']);
});
/*
# Firebase Notification Route
*/
Route::middleware(['auth:api'])->controller(FirebaseTokenController::class)->prefix('firebase')->group(function () {
    Route::post('firebase-token', 'store');
    Route::post('firebase-token/delete', 'destroy');
    Route::post('firebase-token/touch', 'touch');
});

Route::middleware('auth:api')->group(function () {
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/destroy-all', [NotificationController::class, 'destroyAll']);
        Route::delete('/delete-all', [NotificationController::class, 'destroyAll']);
        Route::delete('/{notification}', [NotificationController::class, 'destroy']);
    });
});

// subscribe newsletter
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe']);
Route::post('/contact-us', [ContactController::class, 'store']);



Route::get('/cache_clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('optimize:clear');

    return response()->json(['message' => 'Cache cleared.']);
});



// Public – token link click (no auth required, user clicks from email)
Route::get('/verification/verify-token', [VerificationController::class, 'verifyToken'])->name('verification.token.verify');

// Protected – authenticated user actions
Route::middleware(['auth:api'])->prefix('verification')->name('api.verification.')->controller(VerificationController::class)->group(function () {
    Route::post('send', 'send')->name('send');
    Route::post('verify-otp', 'verifyOtp')->name('verify-otp');
    Route::post('resend', 'resend')->name('resend');
});



Route::post('app/webhooks/revenuecat', RevenueCatWebhookController::class);

//  for broadcast testing
Route::post('/broadcast-test', function () {
    event(new TestBroadcastEvent('Hello from Laravel Reverb 🚀'));

    return 'Broadcast Sent!';
});
Route::post('/notification-test/{user}', [NotificationController::class, 'sendTestNotification']);
