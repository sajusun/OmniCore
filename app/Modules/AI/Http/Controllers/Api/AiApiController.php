<?php

namespace App\Modules\AI\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\AI\Http\Resources\AiConversationResource;
use App\Modules\AI\Http\Resources\AiKnowledgeBaseResource;
use App\Modules\AI\Models\AiKnowledgeBase;
use App\Modules\AI\Services\AiService;
use App\Modules\Ticket\Models\Ticket;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiApiController extends Controller
{
    use ApiResponse;

    public function __construct(protected AiService $aiService) {}

    /**
     * Send a message to the AI Assistant.
     */
    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:4000',
            'conversation_id' => 'nullable|integer|exists:ai_conversations,id',
            'persona' => 'nullable|string|in:support_agent,shopping_assistant,content_writer,ticket_triage',
            'provider' => 'nullable|string|in:gemini,openai,knowledge_base,mock',
        ]);

        $result = $this->aiService->chat(
            user: $request->user(),
            message: $validated['message'],
            conversationId: $validated['conversation_id'] ?? null,
            persona: $validated['persona'] ?? 'support_agent',
            provider: $validated['provider'] ?? null,
        );

        return $this->success([
            'conversation' => new AiConversationResource($result['conversation']),
            'source' => $result['source'],
        ], 'AI response generated successfully.');
    }

    /**
     * List user conversations.
     */
    public function conversations(Request $request): JsonResponse
    {
        $conversations = $request->user()
            ->aiConversations()
            ->with(['latestMessage'])
            ->latest('updated_at')
            ->paginate($request->integer('per_page', 15));

        return $this->paginated(AiConversationResource::collection($conversations));
    }

    /**
     * Show single conversation with messages.
     */
    public function showConversation(Request $request, int $id): JsonResponse
    {
        $conversation = $request->user()
            ->aiConversations()
            ->with(['messages'])
            ->findOrFail($id);

        return $this->success(new AiConversationResource($conversation));
    }

    /**
     * Delete conversation.
     */
    public function deleteConversation(Request $request, int $id): JsonResponse
    {
        $conversation = $request->user()
            ->aiConversations()
            ->findOrFail($id);

        $conversation->delete();

        return $this->success(null, 'Conversation deleted successfully.');
    }

    /**
     * Suggest ticket resolution for support staff or user.
     */
    public function suggestTicketReply(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ticket_id' => 'required|integer|exists:tickets,id',
        ]);

        $ticket = Ticket::with('user')->findOrFail($validated['ticket_id']);
        $suggestion = $this->aiService->suggestTicketReply($ticket);

        return $this->success($suggestion, 'Ticket resolution suggested successfully.');
    }

    /**
     * Triage incoming ticket / classify urgency and category.
     */
    public function triageTicket(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $triage = $this->aiService->classifyTicket($validated['subject'], $validated['message']);

        return $this->success($triage, 'Ticket triaged successfully.');
    }

    /**
     * Generate content (product description, marketing copy, etc.).
     */
    public function generateContent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|string|in:product_description,marketing_email,seo_meta,social_post',
            'prompt' => 'required|string|max:2000',
            'context' => 'nullable|array',
        ]);

        $generated = $this->aiService->generateContent(
            $validated['type'],
            $validated['prompt'],
            $validated['context'] ?? []
        );

        return $this->success($generated, 'Content generated successfully.');
    }

    /**
     * List knowledge-base FAQs.
     */
    public function faqs(Request $request): JsonResponse
    {
        $query = AiKnowledgeBase::active();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('question', 'LIKE', "%{$search}%")
                    ->orWhere('answer', 'LIKE', "%{$search}%");
            });
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        $faqs = $query->orderBy('hit_count', 'desc')->paginate($request->integer('per_page', 20));

        return $this->paginated(AiKnowledgeBaseResource::collection($faqs));
    }
}
