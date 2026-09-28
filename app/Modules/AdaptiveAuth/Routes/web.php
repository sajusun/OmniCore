<?php

declare(strict_types=1);

use App\Modules\AdaptiveAuth\Http\Controllers\Web\AdaptiveAuthWebController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->prefix('adaptive-auth')->name('adaptive.')->group(function () {
    Route::get('/challenge', [AdaptiveAuthWebController::class, 'showChallenge'])->name('challenge');
    Route::post('/verify', [AdaptiveAuthWebController::class, 'verify'])->name('challenge.verify');
    Route::post('/resend', [AdaptiveAuthWebController::class, 'resend'])->name('challenge.resend');
});
