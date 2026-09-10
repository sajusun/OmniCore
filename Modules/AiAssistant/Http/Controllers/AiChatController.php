<?php

namespace Modules\AiAssistant\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\AiAssistant\Models\AiConversation;
use Modules\AiAssistant\Models\AiMessage;
use Modules\AiAssistant\Models\AiPersona;
use Modules\AiAssistant\Services\OllamaStreamService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AiChatController extends Controller
{
    public function __construct(
        protected OllamaStreamService $ollamaService
    ) {}

    public function index()
    {
        $userId = Auth::id();
        $personas = AiPersona::where('user_id', $userId)
            ->orWhere('is_system_default', true)
            ->get();

        $models = $this->ollamaService->listLocalModels();

        return view('aiassistant::index', compact('personas', 'models'));
    }

    public function getAvailableModels(): JsonResponse
    {
        return response()->json([
            'models' => $this->ollamaService->listLocalModels(),
        ]);
    }

    public function getConversations(): JsonResponse
    {
        $conversations = AiConversation::where('user_id', Auth::id())
            ->with(['persona:id,name,icon'])
            ->latest('updated_at')
            ->get();

        return response()->json($conversations);
    }

    public function showConversation(int $id): JsonResponse
    {
        $conversation = AiConversation::where('user_id', Auth::id())
            ->with(['messages', 'persona'])
            ->findOrFail($id);

        return response()->json($conversation);
    }

    public function createConversation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'persona_id' => 'nullable|exists:ai_personas,id',
            'title'      => 'nullable|string|max:120',
            'model'      => 'nullable|string',
        ]);

        $conversation = AiConversation::create([
            'user_id'    => Auth::id(),
            'persona_id' => $validated['persona_id'] ?? null,
            'title'      => $validated['title'] ?? 'New Chat',
            'model_used' => $validated['model'] ?? config('ai-assistant.ollama.default_model'),
        ]);

        return response()->json($conversation->load('persona'), 201);
    }

    public function deleteConversation(int $id): JsonResponse
    {
        $conversation = AiConversation::where('user_id', Auth::id())->findOrFail($id);
        $conversation->delete();

        return response()->json(['status' => 'success']);
    }

    /**
     * Server-Sent Events (SSE) streaming endpoint
     */
    public function streamChat(Request $request): StreamedResponse
    {
        $request->validate([
            'conversation_id' => 'required|exists:ai_conversations,id',
            'message'         => 'required|string',
            'persona_id'      => 'nullable|exists:ai_personas,id',
            'model'           => 'nullable|string',
        ]);

        $conversation = AiConversation::where('user_id', Auth::id())
            ->findOrFail($request->input('conversation_id'));

        $userPrompt = $request->input('message');
        
        // Save incoming User message
        AiMessage::create([
            'conversation_id' => $conversation->id,
            'role'            => 'user',
            'content'         => $userPrompt,
        ]);

        // Auto-title conversation on first interaction
        if ($conversation->title === 'New Chat' || empty($conversation->title)) {
            $conversation->update([
                'title' => mb_strimwidth($userPrompt, 0, 45, '...'),
            ]);
        }

        // Determine Persona & System Prompt
        $persona = null;
        if ($request->filled('persona_id')) {
            $persona = AiPersona::find($request->input('persona_id'));
        } elseif ($conversation->persona_id) {
            $persona = $conversation->persona;
        }

        $systemPrompt = $persona 
            ? $persona->system_prompt 
            : "You are an elite AI Code Assistant. Provide concise, clean, secure code in markdown format.";

        $temperature = $persona ? $persona->temperature : 0.7;
        $model = $request->input('model') ?: ($persona->model ?? $conversation->model_used ?? config('ai-assistant.ollama.default_model'));

        // Retrieve sliding window history
        $slidingLimit = (int) config('ai-assistant.context.sliding_window_size', 10);
        $history = $conversation->getContextMessages($slidingLimit);

        // Prepend Persona System Prompt
        $payloadMessages = array_merge([
            ['role' => 'system', 'content' => $systemPrompt],
        ], $history);

        // Update active model on conversation
        $conversation->update(['model_used' => $model]);

        return response()->stream(function () use ($payloadMessages, $model, $temperature, $conversation) {
            // Disable output buffering
            while (ob_get_level() > 0) {
                ob_end_flush();
            }
            flush();

            $fullAssistantResponse = '';
            $metrics = null;

            $generator = $this->ollamaService->streamChat(
                model: $model,
                messages: $payloadMessages,
                options: ['temperature' => (float)$temperature]
            );

            foreach ($generator as $chunk) {
                if (connection_aborted()) {
                    break;
                }

                if (isset($chunk['error'])) {
                    echo "event: error\n";
                    echo "data: " . json_encode(['message' => $chunk['error']]) . "\n\n";
                    flush();
                    break;
                }

                $content = $chunk['content'] ?? '';
                $fullAssistantResponse .= $content;

                echo "data: " . json_encode([
                    'chunk' => $content,
                    'done'  => $chunk['done'] ?? false,
                ]) . "\n\n";

                if (!empty($chunk['metrics'])) {
                    $metrics = $chunk['metrics'];
                }

                flush();
            }

            // Save completed assistant response
            if (!empty($fullAssistantResponse)) {
                AiMessage::create([
                    'conversation_id'   => $conversation->id,
                    'role'              => 'assistant',
                    'content'           => $fullAssistantResponse,
                    'total_duration_ms' => $metrics['total_duration'] ?? null,
                    'completion_tokens' => $metrics['eval_count'] ?? null,
                ]);

                $conversation->touch();
            }

            echo "event: close\n";
            echo "data: {}\n\n";
            flush();
        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache, no-store, must-revalidate',
            'Connection'        => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}
