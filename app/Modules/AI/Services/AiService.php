<?php

namespace App\Modules\AI\Services;

use App\Models\User;
use App\Modules\AI\Enums\AiPersona;
use App\Modules\AI\Enums\AiProvider;
use App\Modules\AI\Models\AiConversation;
use App\Modules\AI\Models\AiKnowledgeBase;
use App\Modules\AI\Models\AiMessage;
use App\Modules\Ticket\Models\Ticket;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AiService
{
    /**
     * Handle multi-turn conversational chat.
     */
    public function chat(
        User $user,
        string $message,
        ?int $conversationId = null,
        string $persona = 'support_agent',
        ?string $provider = null
    ): array {
        $personaEnum = AiPersona::tryFrom($persona) ?? AiPersona::SUPPORT_AGENT;
        $activeProvider = $this->resolveProvider($provider);

        // Fetch or create conversation
        if ($conversationId) {
            $conversation = $user->aiConversations()->findOrFail($conversationId);
        } else {
            $title = Str::limit($message, 40, '...');
            $conversation = $user->aiConversations()->create([
                'title' => $title,
                'persona' => $personaEnum->value,
                'provider' => $activeProvider->value,
                'context' => ['user_name' => $user->name, 'email' => $user->email],
                'tokens_used' => 0,
            ]);
        }

        // Store user message
        $userTokens = max(1, (int) (strlen($message) / 4));
        $conversation->messages()->create([
            'role' => 'user',
            'content' => $message,
            'tokens' => $userTokens,
        ]);

        // Generate response using active provider
        $replyData = $this->generateReply($conversation, $message, $personaEnum, $activeProvider);

        $assistantTokens = max(1, (int) (strlen($replyData['text']) / 4));

        $assistantMessage = $conversation->messages()->create([
            'role' => 'assistant',
            'content' => $replyData['text'],
            'metadata' => [
                'source' => $replyData['source'],
                'confidence' => $replyData['confidence'] ?? 1.0,
                'matched_kb_id' => $replyData['matched_kb_id'] ?? null,
            ],
            'tokens' => $assistantTokens,
        ]);

        $conversation->increment('tokens_used', $userTokens + $assistantTokens);

        return [
            'conversation' => $conversation->fresh(['messages']),
            'message' => $assistantMessage,
            'source' => $replyData['source'],
        ];
    }

    /**
     * Suggest an auto-reply resolution for a customer support ticket.
     */
    public function suggestTicketReply(Ticket $ticket): array
    {
        $kbMatch = $this->matchKnowledgeBase($ticket->subject . ' ' . $ticket->description);

        if ($kbMatch) {
            return [
                'suggested_reply' => "Hello {$ticket->user?->name},\n\n" . $kbMatch->answer . "\n\nBest regards,\nCustomer Support Team",
                'confidence' => 0.95,
                'source' => 'knowledge_base',
                'matched_kb_id' => $kbMatch->id,
                'recommended_status' => 'resolved',
            ];
        }

        $reply = "Hello {$ticket->user?->name},\n\nThank you for reaching out regarding '{$ticket->subject}'. We have received your inquiry and our team is actively investigating this. We will get back to you shortly.\n\nBest regards,\nCustomer Support Team";

        return [
            'suggested_reply' => $reply,
            'confidence' => 0.85,
            'source' => 'ai_triage',
            'matched_kb_id' => null,
            'recommended_status' => 'in_progress',
        ];
    }

    /**
     * Classify priority and triage category of an incoming ticket or request.
     */
    public function classifyTicket(string $subject, string $message): array
    {
        $combined = strtolower($subject . ' ' . $message);
        
        $priority = 'medium';
        $confidence = 0.80;

        if (Str::contains($combined, ['urgent', 'emergency', 'hacked', 'stolen', 'payment failed', 'charged twice', 'critical', 'crash'])) {
            $priority = 'urgent';
            $confidence = 0.95;
        } elseif (Str::contains($combined, ['bug', 'error', 'broken', 'not working', 'fail', 'refund', 'dispute'])) {
            $priority = 'high';
            $confidence = 0.90;
        } elseif (Str::contains($combined, ['how to', 'question', 'info', 'inquiry', 'feature request', 'feedback'])) {
            $priority = 'low';
            $confidence = 0.85;
        }

        return [
            'priority' => $priority,
            'confidence' => $confidence,
            'sentiment' => Str::contains($combined, ['urgent', 'angry', 'terrible', 'worst', 'scam']) ? 'negative' : 'neutral',
        ];
    }

    /**
     * Generate e-commerce or marketing content.
     */
    public function generateContent(string $type, string $prompt, array $context = []): array
    {
        $productName = $context['product_name'] ?? 'Premium Item';
        $category = $context['category'] ?? 'General';
        $tone = $context['tone'] ?? 'professional and engaging';

        switch ($type) {
            case 'product_description':
                $content = "Discover the unparalleled excellence of **{$productName}** in our {$category} collection. Crafted with precision and premium quality materials, it elevates your everyday experience. Key features include modern aesthetics, exceptional durability, and effortless usability. Order now to experience top-tier quality.";
                break;
            case 'marketing_email':
                $content = "Subject: Special Exclusive Deal on {$productName}!\n\nHey there,\n\nWe noticed you have great taste. For a limited time only, elevate your experience with {$productName}. Enjoy premium quality, fast shipping, and our 100% satisfaction guarantee.\n\nShop now before stocks run out!\n\nCheers,\nThe OmniCore Team";
                break;
            case 'seo_meta':
                $content = "Shop {$productName} online at best prices. High-quality {$category} with authentic guarantee and fast nationwide delivery.";
                break;
            case 'social_post':
                $content = "Upgrade your lifestyle with {$productName}! ✨ Crafted for excellence and designed to impress. Tap the link in bio to get yours today! 🔥 #Ecommerce #Trending #{$category}";
                break;
            default:
                $content = "AI Generated Content for: {$prompt}. Optimized for {$tone} communication.";
        }

        return [
            'type' => $type,
            'content' => $content,
            'tokens' => max(1, (int) (strlen($content) / 4)),
            'provider' => 'mock',
        ];
    }

    /**
     * Search knowledge base for exact or semantic keyword matches.
     */
    public function matchKnowledgeBase(string $query): ?AiKnowledgeBase
    {
        $cleaned = trim(strtolower($query));
        $words = array_filter(explode(' ', preg_replace('/[^\w\s]/', '', $cleaned)), fn($w) => strlen($w) > 2);

        // 1. Direct question match
        $direct = AiKnowledgeBase::active()
            ->where('question', 'LIKE', "%{$cleaned}%")
            ->first();

        if ($direct) {
            $direct->increment('hit_count');
            return $direct;
        }

        // 2. Keyword match
        $allKb = AiKnowledgeBase::active()->get();
        $bestMatch = null;
        $maxScore = 0;

        foreach ($allKb as $kb) {
            $score = 0;
            $kbKeywords = (array) ($kb->keywords ?? []);
            $kbQuestion = strtolower($kb->question);

            foreach ($words as $w) {
                if (str_contains($kbQuestion, $w)) {
                    $score += 3;
                }
                foreach ($kbKeywords as $kw) {
                    if (str_contains(strtolower($kw), $w)) {
                        $score += 2;
                    }
                }
            }

            if ($score > $maxScore && $score >= 3) {
                $maxScore = $score;
                $bestMatch = $kb;
            }
        }

        if ($bestMatch) {
            $bestMatch->increment('hit_count');
        }

        return $bestMatch;
    }

    /**
     * Resolve default AI provider based on environment configuration.
     */
    protected function resolveProvider(?string $requested): AiProvider
    {
        if ($requested && $enum = AiProvider::tryFrom($requested)) {
            return $enum;
        }

        if (app()->runningUnitTests()) {
            return AiProvider::KNOWLEDGE_BASE;
        }

        if (env('GEMINI_API_KEY') && env('GEMINI_API_KEY') !== 'your-gemini-key') {
            return AiProvider::GEMINI;
        }

        if (env('OPENAI_API_KEY') && env('OPENAI_API_KEY') !== 'your-openai-key') {
            return AiProvider::OPENAI;
        }

        return AiProvider::KNOWLEDGE_BASE;
    }

    /**
     * Internal reply generator.
     */
    protected function generateReply(
        AiConversation $conversation,
        string $message,
        AiPersona $persona,
        AiProvider $provider
    ): array {
        // First check knowledge base
        $kb = $this->matchKnowledgeBase($message);
        if ($kb) {
            return [
                'text' => $kb->answer,
                'source' => 'knowledge_base',
                'confidence' => 0.98,
                'matched_kb_id' => $kb->id,
            ];
        }

        // Provider: Gemini
        if (!app()->runningUnitTests() && $provider === AiProvider::GEMINI && env('GEMINI_API_KEY') && env('GEMINI_API_KEY') !== 'your-gemini-key') {
            try {
                $response = Http::timeout(5)->post(
                    'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . env('GEMINI_API_KEY'),
                    [
                        'contents' => [
                            ['parts' => [['text' => $persona->systemPrompt() . "\nUser: " . $message]]]
                        ]
                    ]
                );

                if ($response->successful()) {
                    $text = $response->json('candidates.0.content.parts.0.text');
                    if ($text) {
                        return ['text' => trim($text), 'source' => 'gemini', 'confidence' => 0.95];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Gemini API call failed: ' . $e->getMessage());
            }
        }

        // Provider: OpenAI
        if (!app()->runningUnitTests() && $provider === AiProvider::OPENAI && env('OPENAI_API_KEY') && env('OPENAI_API_KEY') !== 'your-openai-key') {
            try {
                $response = Http::timeout(5)->withToken(env('OPENAI_API_KEY'))->post(
                    'https://api.openai.com/v1/chat/completions',
                    [
                        'model' => 'gpt-4o-mini',
                        'messages' => [
                            ['role' => 'system', 'content' => $persona->systemPrompt()],
                            ['role' => 'user', 'content' => $message],
                        ],
                    ]
                );

                if ($response->successful()) {
                    $text = $response->json('choices.0.message.content');
                    if ($text) {
                        return ['text' => trim($text), 'source' => 'openai', 'confidence' => 0.95];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('OpenAI API call failed: ' . $e->getMessage());
            }
        }

        // Fallback Mock / Intelligent Assistant Answer
        $smartReplies = [
            AiPersona::SUPPORT_AGENT->value => "Hello! Thank you for reaching out to OmniCore support. I understand you're asking about \"{$message}\". How can I further assist you with this today?",
            AiPersona::SHOPPING_ASSISTANT->value => "Welcome to OmniCore Shopping Assistant! Based on your query \"{$message}\", I can help you find the best trending products, deals, and vendor offers.",
            AiPersona::CONTENT_WRITER->value => "Here is a compelling draft tailored for \"{$message}\": Elevate your lifestyle with top-tier quality and seamless performance today!",
            AiPersona::TICKET_TRIAGE->value => "Ticket analysis completed for: \"{$message}\". Prioritized for fast resolution.",
        ];

        $reply = $smartReplies[$persona->value] ?? $smartReplies[AiPersona::SUPPORT_AGENT->value];

        return [
            'text' => $reply,
            'source' => 'ai_assistant',
            'confidence' => 0.90,
        ];
    }
}
