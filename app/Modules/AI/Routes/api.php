<?php

use App\Modules\AI\Http\Controllers\Api\AiApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/ai')->name('api.v1.ai.')->group(function () {
    // Public FAQ knowledge base
    Route::get('/faqs', [AiApiController::class, 'faqs'])->name('faqs');

    // Authenticated AI capabilities
    Route::middleware(['auth:api'])->group(function () {
        Route::post('/chat', [AiApiController::class, 'chat'])->name('chat');
        Route::get('/conversations', [AiApiController::class, 'conversations'])->name('conversations.index');
        Route::get('/conversations/{id}', [AiApiController::class, 'showConversation'])->name('conversations.show');
        Route::delete('/conversations/{id}', [AiApiController::class, 'deleteConversation'])->name('conversations.destroy');

        Route::post('/ticket-suggest', [AiApiController::class, 'suggestTicketReply'])->name('ticket.suggest');
        Route::post('/ticket-triage', [AiApiController::class, 'triageTicket'])->name('ticket.triage');
        Route::post('/generate-content', [AiApiController::class, 'generateContent'])->name('generate.content');
    });
});
