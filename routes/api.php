<?php

use App\Events\TestBroadcastEvent;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Api\FollowController;
use App\Http\Controllers\Api\FriendController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\Auth\UserController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Chat\BlockController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Chat\MessageController;
use App\Http\Controllers\Api\Frontend\CmsController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\VerificationController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Chat\ChatRoomController;
use App\Http\Controllers\Api\FirebaseTokenController;
use App\Http\Controllers\Api\Frontend\ClubController;
use App\Http\Controllers\Api\Frontend\PostController;
use App\Http\Controllers\Api\Frontend\EventController;
use App\Http\Controllers\Api\Auth\SocialLoginController;
use App\Http\Controllers\Api\Chat\ChatSettingController;
use App\Http\Controllers\Api\Frontend\VehicleController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\Frontend\PostCommentController;
use App\Http\Controllers\Api\Webhooks\RevenueCatWebhookController;


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
    Route::post('/update-profile', [UserController::class, 'updateProfile']);
    Route::post('/onboarding-fill', [UserController::class, 'onboardingUpdate']);
    Route::post('/update-avatar', [UserController::class, 'updateAvatar']);
    Route::post('/update-password', [UserController::class, 'updatePassword']);
    Route::delete('/profile/delete', [UserController::class, 'destroy']);
});
Route::get('/header-test', function (Illuminate\Http\Request $request) {
    return response()->json([
        'authorization' => $request->header('Authorization'),
        'bearer'       => $request->bearerToken(),
    ]);
});

Route::middleware('auth:api')->prefix('friends')->group(function () {

    // Friend Request
    Route::post('/request/{user}', [FriendController::class, 'sendRequest']);
    Route::post('/accept/{friendRequest}', [FriendController::class, 'accept']);
    Route::post('/reject/{friendRequest}', [FriendController::class, 'reject']);
    Route::delete('/cancel/{friendRequest}', [FriendController::class, 'cancel']);

    // Friend
    Route::delete('/unfriend/{user}', [FriendController::class, 'unfriend']);
    Route::get('/', [FriendController::class, 'friends']);

    // Requests
    Route::get('/requests/pending', [FriendController::class, 'pendingRequests']);
    Route::get('/requests/sent', [FriendController::class, 'sentRequests']);
});

Route::middleware('auth:api')->group(function () {
    Route::post('/users/{user}/follow', [FollowController::class, 'follow']);
    Route::delete('/users/{user}/unfollow', [FollowController::class, 'unfollow']);
    Route::post('/users/{user}/toggle-follow', [FollowController::class, 'toggle']);
    Route::get('/users/followers', [FollowController::class, 'followers']);
    Route::get('/users/followings', [FollowController::class, 'followings']);
});

Route::middleware('auth:api')->group(function () {
    Route::get('/vehicles/search', [VehicleController::class, 'search']);
    Route::get('/garages/{garageId}/vehicles', [VehicleController::class, 'getByGarage']);
    Route::delete('/vehicles/media/{media}', [VehicleController::class, 'deleteImage']);

    Route::get('/vehicles', [VehicleController::class, 'index']);
    Route::get('/vehicle-list-for-rsvp', [VehicleController::class, 'miniVehicleData']);
    Route::post('/vehicles/store', [VehicleController::class, 'store']);
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
    Route::delete('/events/{event}/rsvp', [EventController::class, 'cancelRsvp']);
    Route::get('/events/{event}/rsvps', [EventController::class, 'rsvps']);

    Route::get('/events/{event}/rsvps/matching', [EventController::class, 'matchingParts']);
    Route::get('/events/{event}/rsvps/all-parts', [EventController::class, 'allParts']);

    // Bookmark
    Route::post('/events/{event}/bookmark', [EventController::class, 'toggleBookmark']);
});

Route::middleware(['auth:api'])->controller(PostController::class)->prefix('/posts')->group(function () {
    Route::get('/', 'index');
    Route::post('/store', 'store');
    Route::get('/{post}/show', 'show');
    Route::post('/{post}/update', 'update');
    Route::delete('/{post}/delete', 'destroy');
    Route::post('/{post}/like', 'like');
    Route::post('/{post}/repost', 'repost');
    Route::post('/{post}/save', 'toggleSave');
    Route::get('/saved-posts', 'savedPosts');
    Route::get('/share/{share_link}', 'share')->name('post.share');

    Route::get('/feeds', 'feed');
});

Route::middleware('auth:api')->group(function () {
    Route::get('/posts/{post}/comments', [PostCommentController::class, 'index']);
    Route::post('/posts/{post}/comments', [PostCommentController::class, 'store']);
    Route::post('/comments/{comment}/update', [PostCommentController::class, 'update']);
    Route::delete('/comments/{comment}/delete', [PostCommentController::class, 'destroy']);
    Route::post('/comments/{comment}/reply', [PostCommentController::class, 'reply']);
    Route::post('/comments/{comment}/like', [PostCommentController::class, 'toggleLike']);
    Route::get('/comments/{comment}/replies', [PostCommentController::class, 'replies']);
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
        Route::delete('/{notification}', [NotificationController::class, 'destroy']);
    });
});

// subscribe newsletter
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe']);
Route::post('/contact-us', [ContactController::class, 'store']);

Route::prefix('cms')->name('cms.')->group(function () {
    Route::get('/', [CmsController::class, 'index'])->name('index');   // All pages & their sections
    Route::get('{page}', [CmsController::class, 'page'])->name('page');    // All sections of a page
    Route::get('{page}/{section}', [CmsController::class, 'section'])->name('section'); // Single section
});

Route::get('/cache_clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('optimize:clear');

    return response()->json(['message' => 'Cache cleared.']);
});

// others loaded routes
require app_path('Modules/Media/Routes/api.php');

// Public – token link click (no auth required, user clicks from email)
Route::get('/verification/verify-token', [VerificationController::class, 'verifyToken'])->name('verification.token.verify');

// Protected – authenticated user actions
Route::middleware(['auth:api'])->prefix('verification')->name('api.verification.')->controller(VerificationController::class)->group(function () {
    Route::post('send', 'send')->name('send');
    Route::post('verify-otp', 'verifyOtp')->name('verify-otp');
    Route::post('resend', 'resend')->name('resend');
});

// Chat Module Routes
Route::middleware('auth:api')->prefix('chat')->group(function () {
    // Rooms
    Route::post('/rooms/single', [ChatRoomController::class, 'single']);
    Route::post('/rooms/group', [ChatRoomController::class, 'group']);
    Route::post('/rooms/channel', [ChatRoomController::class, 'channel']);
    Route::get('/rooms', [ChatRoomController::class, 'index']);
    Route::get('/rooms/{room}', [ChatRoomController::class, 'show']);
    Route::patch('/rooms/{room}', [ChatRoomController::class, 'update']);
    Route::delete('/rooms/{room}', [ChatRoomController::class, 'destroy']);

    // Participants
    Route::post('/rooms/{room}/participants', [ChatRoomController::class, 'addParticipants']);
    Route::delete('/rooms/{room}/participants/{user}', [ChatRoomController::class, 'removeParticipant']);
    Route::post('/rooms/{room}/leave', [ChatRoomController::class, 'leave']);
    Route::post('/channels/{room}/join', [ChatRoomController::class, 'join']);

    // Messages
    Route::get('/rooms/{room}/messages', [MessageController::class, 'index']);
    Route::post('/messages', [MessageController::class, 'send']);
    Route::patch('/messages/{message}', [MessageController::class, 'update']);
    Route::delete('/messages/{message}', [MessageController::class, 'destroy']);

    // Blocks
    Route::post('/block/{user}', [BlockController::class, 'block']);
    Route::delete('/unblock/{user}', [BlockController::class, 'unblock']);
    Route::get('/blocked-users', [BlockController::class, 'blockedUsers']);

    // Settings
    Route::patch('/rooms/{room}/settings/notification', [ChatSettingController::class, 'updateNotification']);
    Route::patch('/rooms/{room}/settings/sound', [ChatSettingController::class, 'updateSound']);
    Route::patch('/rooms/{room}/settings/mute', [ChatSettingController::class, 'mute']);
    Route::delete('/rooms/{room}/settings/mute', [ChatSettingController::class, 'unmute']);
});

Route::post('app/webhooks/revenuecat', RevenueCatWebhookController::class);



//  for broadcast testing
Route::post('/broadcast-test', function () {
    event(new TestBroadcastEvent('Hello from Laravel Reverb 🚀'));
    return 'Broadcast Sent!';
});
Route::post('/notification-test/{user}', [NotificationController::class, 'sendTestNotification']);
