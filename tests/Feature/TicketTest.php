<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Ticket\Enums\TicketPriority;
use App\Modules\Ticket\Enums\TicketStatus;
use App\Modules\Ticket\Models\Ticket;
use App\Modules\Ticket\Models\TicketCategory;
use App\Modules\Ticket\Services\TicketService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected User $staff;
    protected string $token;
    protected TicketCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure roles exist
        Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Staff', 'guard_name' => 'web']);

        $this->user = User::factory()->create([
            'status' => 'active',
        ]);

        $this->staff = User::factory()->create([
            'status' => 'active',
        ]);
        $this->staff->assignRole('Super Admin');

        $this->token = auth('api')->login($this->user);

        $this->category = TicketCategory::firstOrCreate(
            ['slug' => 'billing-payments-test'],
            ['name' => 'Billing & Payments Test', 'description' => 'Test Category', 'is_active' => true]
        );
    }

    public function test_can_fetch_ticket_categories(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/v1/tickets/categories');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'code'   => 200,
            ])
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'slug', 'description', 'is_active']
                ]
            ]);
    }

    public function test_user_can_create_ticket_with_attachment(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('error_screenshot.png', 100, 'image/png');

        $payload = [
            'subject'     => 'Payment deduction failed',
            'category_id' => $this->category->id,
            'priority'    => TicketPriority::HIGH->value,
            'message'     => 'My payment was deducted from bank but order not confirmed.',
            'attachments' => [$file],
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/v1/tickets', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'status' => true,
                'code'   => 201,
                'data'   => [
                    'subject'  => 'Payment deduction failed',
                    'priority' => 'high',
                    'status'   => 'open',
                ]
            ]);

        $this->assertDatabaseHas('tickets', [
            'user_id' => $this->user->id,
            'subject' => 'Payment deduction failed',
            'status'  => 'open',
        ]);

        $ticket = Ticket::where('user_id', $this->user->id)->first();
        $this->assertCount(1, $ticket->attachments);
    }

    public function test_user_can_list_their_tickets(): void
    {
        Ticket::create([
            'user_id'     => $this->user->id,
            'category_id' => $this->category->id,
            'priority'    => TicketPriority::MEDIUM->value,
            'status'      => TicketStatus::OPEN->value,
            'subject'     => 'Inquiry about order delivery',
            'message'     => 'When will my package arrive?',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/v1/tickets');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'code'   => 200,
            ])
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'ticket_number', 'subject', 'status', 'priority']
                ],
                'pagination' => ['type', 'current_page', 'last_page', 'per_page', 'total']
            ]);
    }

    public function test_user_can_view_ticket_thread_and_reply(): void
    {
        $ticket = Ticket::create([
            'user_id'     => $this->user->id,
            'category_id' => $this->category->id,
            'priority'    => TicketPriority::LOW->value,
            'status'      => TicketStatus::OPEN->value,
            'subject'     => 'Need invoice PDF',
            'message'     => 'Please generate and send me my latest invoice PDF.',
        ]);

        // View ticket
        $viewResponse = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/v1/tickets/' . $ticket->id);

        $viewResponse->assertStatus(200)
            ->assertJson([
                'status' => true,
                'code'   => 200,
                'data'   => [
                    'id'      => $ticket->id,
                    'subject' => 'Need invoice PDF',
                ]
            ]);

        // Reply to ticket
        $replyResponse = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/v1/tickets/' . $ticket->id . '/reply', [
                'message' => 'Additional detail: invoice #INV-9992',
            ]);

        $replyResponse->assertStatus(201)
            ->assertJson([
                'status' => true,
                'code'   => 201,
                'data'   => [
                    'message' => 'Additional detail: invoice #INV-9992',
                ]
            ]);

        $this->assertDatabaseHas('ticket_replies', [
            'ticket_id' => $ticket->id,
            'user_id'   => $this->user->id,
            'message'   => 'Additional detail: invoice #INV-9992',
        ]);
    }

    public function test_user_can_close_ticket(): void
    {
        $ticket = Ticket::create([
            'user_id'     => $this->user->id,
            'category_id' => $this->category->id,
            'priority'    => TicketPriority::LOW->value,
            'status'      => TicketStatus::OPEN->value,
            'subject'     => 'Resolved matter',
            'message'     => 'Already found the solution, thanks.',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/v1/tickets/' . $ticket->id . '/close');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'code'   => 200,
                'data'   => [
                    'status' => 'closed',
                ]
            ]);

        $this->assertEquals(TicketStatus::CLOSED, $ticket->fresh()->status);
    }

    public function test_ticket_service_staff_assign_and_internal_note(): void
    {
        $ticketService = app(TicketService::class);

        $ticket = $ticketService->createTicket($this->user, [
            'subject'  => 'Complex account issue',
            'priority' => TicketPriority::URGENT,
            'message'  => 'Need engineering assistance on this.',
        ]);

        // Assign staff
        $ticketService->assignTicket($ticket, $this->staff);
        $this->assertEquals($this->staff->id, $ticket->fresh()->assigned_to);
        $this->assertEquals(TicketStatus::IN_PROGRESS, $ticket->fresh()->status);

        // Staff internal note
        $reply = $ticketService->replyToTicket(
            $ticket,
            $this->staff,
            'Engineering investigating database locks.',
            isInternalNote: true
        );

        $this->assertTrue($reply->is_internal_note);
        $this->assertDatabaseHas('ticket_replies', [
            'ticket_id'        => $ticket->id,
            'is_internal_note' => true,
        ]);
    }
}
