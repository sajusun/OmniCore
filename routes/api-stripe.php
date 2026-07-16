<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Gateway\Stripe\StripeWebHookController;
use App\Http\Controllers\Api\Gateway\Stripe\StripeCallBackController;
use App\Http\Controllers\Api\Gateway\Stripe\StripeOnBoardingController;
use App\Http\Controllers\Api\Gateway\Stripe\StripeWebHookHoldController;
use App\Http\Controllers\Api\Gateway\Stripe\StripeSubscriptionsController;
use App\Http\Controllers\Api\Gateway\Stripe\StripeWebHookSpliteController;

/*
# Stripe routes
*/

Route::prefix('api')->name('api.')->group(function () {

    // //stripe callback
    // Route::controller(StripeCallBackController::class)->prefix('payment/stripe')->name('payment.stripe.')->group(function () {
    //     Route::post('/checkout', 'checkout')->middleware(['auth:api']);
    //     Route::get('/success', 'success')->name('success');
    //     Route::get('/cancel', 'failure')->name('cancel');
    // });

    // //stripe webhook
    // Route::controller(StripeWebHookController::class)->prefix('payment/stripe')->name('payment.stripe.')->group(function () {
    //     Route::post('/intent', 'intent')->middleware(['auth:api']);
    //     Route::post('/webhook', 'webhook');
    // });

    // //stripe split webhook
    // Route::controller(StripeWebHookSpliteController::class)->prefix('payment/stripe/split')->name('payment.stripe.split.')->group(function () {
    //     Route::get('/intent/{booking_id}', 'intent')->name('intent');
    //     Route::post('/webhook', 'webhook')->name('webhook');
    // });

    // //stripe account
    // Route::controller(StripeOnBoardingController::class)->prefix('payment/stripe/account')->name('payment.stripe.account.')->group(function () {
    //     Route::middleware(['auth:api'])->get('/connect', 'accountConnect')->name('connect');
    //     Route::get('/connect/success/{account_id}', 'accountSuccess')->name('connect.success');
    //     Route::get('/connect/refresh/{account_id}', 'accountRefresh')->name('connect.refresh');
    //     Route::middleware(['auth:api'])->get('/url', 'AccountUrl')->name('url');
    //     Route::middleware(['auth:api'])->get('/info', 'accountInfo')->name('info');
    //     Route::middleware(['auth:api'])->post('/withdraw', 'withdraw')->name('withdraw');
    // });

    // //stripe subscriptions
    // Route::controller(StripeSubscriptionsController::class)->prefix('payment/stripe/subscriptions')->name('payment.stripe.subscriptions.')->group(function () {
    //     Route::post('/plan', 'plan');
    //     Route::get('/my/plan', 'myPlan');
    //     Route::get('/cancel/plan', 'cancelPlan');
    // });


    // Route::controller(StripeWebHookHoldController::class)->prefix('payment/stripe/payment/hold')->name('payment.stripe.payment.hold')->group(function () {
    //     Route::post('/create', 'createPaymentHold')->name('create');
    //     Route::post('/capture', 'capturePayment')->name('capture');
    //     Route::post('/cancel', 'cancelPaymentHold')->name('cancel');
    // });
    
});
