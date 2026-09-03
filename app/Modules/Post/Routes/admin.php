<?php

use App\Modules\Post\Http\Controllers\Backend\PostController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Post Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware('permission:post.list|post.show|post.delete')
    ->prefix('posts')
    ->name('posts.')
    ->group(function () {
        Route::get('/', [PostController::class, 'index'])->name('index');
        Route::get('/{post}', [PostController::class, 'show'])->name('show');
        Route::delete('/{post}/destroy', [PostController::class, 'destroy'])->name('destroy');
    });
