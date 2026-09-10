<?php

use Illuminate\Support\Facades\Route;
use Modules\AiAssistant\Http\Controllers\AiChatController;
use Modules\AiAssistant\Http\Controllers\AiPersonaController;

Route::get('/conversations', [AiChatController::class, 'getConversations']);
Route::post('/conversations', [AiChatController::class, 'createConversation']);
Route::get('/conversations/{id}', [AiChatController::class, 'showConversation']);
Route::delete('/conversations/{id}', [AiChatController::class, 'deleteConversation']);
Route::post('/chat/stream', [AiChatController::class, 'streamChat']);
Route::get('/personas', [AiPersonaController::class, 'index']);
Route::post('/personas', [AiPersonaController::class, 'store']);
