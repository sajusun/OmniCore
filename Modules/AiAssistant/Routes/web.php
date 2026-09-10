<?php

use Illuminate\Support\Facades\Route;
use Modules\AiAssistant\Http\Controllers\AiChatController;
use Modules\AiAssistant\Http\Controllers\AiPersonaController;

Route::get('/', [AiChatController::class, 'index'])->name('index');
Route::get('/models', [AiChatController::class, 'getAvailableModels'])->name('models');

// Conversations
Route::get('/conversations', [AiChatController::class, 'getConversations'])->name('conversations.index');
Route::post('/conversations', [AiChatController::class, 'createConversation'])->name('conversations.store');
Route::get('/conversations/{id}', [AiChatController::class, 'showConversation'])->name('conversations.show');
Route::delete('/conversations/{id}', [AiChatController::class, 'deleteConversation'])->name('conversations.destroy');

// SSE Chat Stream
Route::post('/chat/stream', [AiChatController::class, 'streamChat'])->name('chat.stream');

// Personas
Route::get('/personas', [AiPersonaController::class, 'index'])->name('personas.index');
Route::post('/personas', [AiPersonaController::class, 'store'])->name('personas.store');
Route::delete('/personas/{id}', [AiPersonaController::class, 'destroy'])->name('personas.destroy');
