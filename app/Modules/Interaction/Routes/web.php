<?php

use App\Modules\Interaction\Http\Controllers\Web\ShareRedirectController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Share Routes (Public)
|--------------------------------------------------------------------------
*/

// Private / Expiring / Single-Use Token resolver
Route::get('/s/{token}', [ShareRedirectController::class, 'resolvePrivate'])->name('share.private');

// Public SEO-friendly slug resolver
Route::get('/share/{type}/{slug}', [ShareRedirectController::class, 'resolvePublic'])->name('share.public');
