<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Media\Controllers\MediaController;

Route::prefix('media')->middleware('auth:api')->group(function () {
    Route::get('/', [MediaController::class, 'index']);
    Route::get('/{id}', [MediaController::class, 'show']);
    Route::delete('/', [MediaController::class, 'destroy']);
});

// how to load this route file in base route file (routes/api.php)

//     require app_path(
//     'Modules/Media/Routes/api.php'
// );