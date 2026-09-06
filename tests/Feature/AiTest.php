<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\AI\Models\AiConversation;
use App\Modules\AI\Models\AiKnowledgeBase;
use App\Modules\Ticket\Models\Ticket;
use App\Modules\Ticket\Models\TicketCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => 'ai_user_' . uniqid() . '@example.com',
        ]);

        $this->token = auth('api')->login($this->user);

        // Seed basic knowledge base
        AiKnowledgeBase::create([
            'category' => 'Orders',
            'question' => 'How can I track my order?',
            'answer' => 'Go to My Orders and click Track Order.',
            'keywords' => ['track', 'order', 'shipping'],
            'is_active' => true,
        ]);

        AiKnowledgeBase::create([
            'category' => 'Refunds',
            'question' => 'What is your refund policy?',
            'answer' => 'We offer 30-day money back guarantee.',
            'keywords' => ['refund', 'return', 'policy'],
            'is_active' => true,
        ]);
    }

    public function test_user_can_chat_with_ai_and_match_knowledge_base(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/v1/ai/chat', [
                'message' => 'How can I track my order?',
                'persona' => 'support_agent',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.source', 'knowledge_base');

        $this->assertDatabaseHas('ai_conversations', [
            'user_id' => $this->user->id,
        ]);

        $this->assertDatabaseHas('ai_messages', [
            'role' => 'user',
            'content' => 'How can I track my order?',
        ]);

        $this->assertDatabaseHas('ai_messages', [
            'role' => 'assistant',
            'content' => 'Go to My Orders and click Track Order.',
        ]);
    }

    public function test_ai_maintains_multi_turn_conversation(): void
    {
        // 1st message
        $first = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/v1/ai/chat', [
                'message' => 'Hello, I need help with something',
                'persona' => 'support_agent',
            ]);

        $first->assertStatus(200);
        $conversationId = $first->json('data.conversation.id');

        // 2nd message on same conversation
        $second = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/v1/ai/chat', [
                'message' => 'What is your refund policy?',
                'conversation_id' => $conversationId,
            ]);

        $second->assertStatus(200)
            ->assertJsonPath('data.conversation.id', $conversationId);

        $this->assertEquals(4, AiConversation::find($conversationId)->messages()->count());
    }

    public function test_ai_can_suggest_ticket_resolution(): void
    {
        $category = TicketCategory::create([
            'name' => 'Shipping Issue',
            'slug' => 'shipping-issue',
        ]);

        $ticket = Ticket::create([
            'user_id' => $this->user->id,
            'ticket_category_id' => $category->id,
            'ticket_number' => 'TICK-AI-101',
            'subject' => 'How can I track my order online?',
            'message' => 'I placed an order yesterday and want to know the tracking status.',
            'priority' => 'medium',
            'status' => 'open',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/v1/ai/ticket-suggest', [
                'ticket_id' => $ticket->id,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', true)
            ->assertJsonStructure([
                'data' => ['suggested_reply', 'confidence', 'source', 'recommended_status']
            ]);

        $this->assertStringContainsString('Track Order', $response->json('data.suggested_reply'));
    }

    public function test_ai_can_triage_and_classify_ticket_urgency(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/v1/ai/ticket-triage', [
                'subject' => 'URGENT: Charged twice and payment failed on checkout',
                'message' => 'My card was debited two times for the same order!',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.priority', 'urgent')
            ->assertJsonPath('data.sentiment', 'negative');
    }

    public function test_ai_can_generate_ecommerce_marketing_content(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/v1/ai/generate-content', [
                'type' => 'product_description',
                'prompt' => 'Write a product description for Wireless Noise Cancelling Headphones',
                'context' => [
                    'product_name' => 'Sony WH-1000XM5',
                    'category' => 'Electronics & Audio',
                ],
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', true)
            ->assertJsonStructure([
                'data' => ['type', 'content', 'tokens', 'provider']
            ]);

        $this->assertStringContainsString('Sony WH-1000XM5', $response->json('data.content'));
    }

    public function test_public_user_can_browse_and_search_knowledge_base(): void
    {
        $response = $this->getJson('/api/v1/ai/faqs?search=refund');

        $response->assertStatus(200)
            ->assertJsonPath('status', true)
            ->assertJsonCount(1, 'data');
    }
}
