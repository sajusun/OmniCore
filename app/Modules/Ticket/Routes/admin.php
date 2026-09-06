<?php

use App\Modules\Ticket\Controllers\Admin\CannedResponseAdminController;
use App\Modules\Ticket\Controllers\Admin\TicketAdminController;
use App\Modules\Ticket\Controllers\Admin\TicketCategoryAdminController;
use Illuminate\Support\Facades\Route;

Route::middleware('permission:ticket.list|ticket.reply|ticket.close')->prefix('tickets')->name('tickets.')->group(function () {
    Route::get('/', [TicketAdminController::class, 'index'])->name('index');
    Route::get('/{ticket}', [TicketAdminController::class, 'show'])->name('show');
    Route::post('/{ticket}/reply', [TicketAdminController::class, 'reply'])->middleware('permission:ticket.reply')->name('reply');
    Route::patch('/{ticket}/assign', [TicketAdminController::class, 'assign'])->name('assign');
    Route::patch('/{ticket}/status', [TicketAdminController::class, 'updateStatus'])->name('status');

    // Categories
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [TicketCategoryAdminController::class, 'index'])->name('index');
        Route::post('/', [TicketCategoryAdminController::class, 'store'])->name('store');
        Route::put('/{category}', [TicketCategoryAdminController::class, 'update'])->name('update');
        Route::delete('/{category}', [TicketCategoryAdminController::class, 'destroy'])->name('destroy');
    });

    // Canned Responses
    Route::prefix('canned-responses')->name('canned-responses.')->group(function () {
        Route::get('/', [CannedResponseAdminController::class, 'index'])->name('index');
        Route::post('/', [CannedResponseAdminController::class, 'store'])->name('store');
        Route::put('/{cannedResponse}', [CannedResponseAdminController::class, 'update'])->name('update');
        Route::delete('/{cannedResponse}', [CannedResponseAdminController::class, 'destroy'])->name('destroy');
    });
});
