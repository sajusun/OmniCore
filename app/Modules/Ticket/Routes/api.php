<?php

use App\Modules\Ticket\Controllers\Api\TicketApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/tickets')->middleware(['auth:api'])->group(function () {
    Route::get('/categories', [TicketApiController::class, 'categories'])->name('api.tickets.categories');
    Route::get('/', [TicketApiController::class, 'index'])->name('api.tickets.index');
    Route::post('/', [TicketApiController::class, 'store'])->name('api.tickets.store');
    Route::get('/{ticket}', [TicketApiController::class, 'show'])->name('api.tickets.show');
    Route::post('/{ticket}/reply', [TicketApiController::class, 'reply'])->name('api.tickets.reply');
    Route::post('/{ticket}/close', [TicketApiController::class, 'close'])->name('api.tickets.close');
});
