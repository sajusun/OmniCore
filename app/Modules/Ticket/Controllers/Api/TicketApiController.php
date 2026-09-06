<?php

declare(strict_types=1);

namespace App\Modules\Ticket\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Ticket\Enums\TicketPriority;
use App\Modules\Ticket\Models\Ticket;
use App\Modules\Ticket\Models\TicketCategory;
use App\Modules\Ticket\Resources\TicketCategoryResource;
use App\Modules\Ticket\Resources\TicketDetailResource;
use App\Modules\Ticket\Resources\TicketReplyResource;
use App\Modules\Ticket\Resources\TicketResource;
use App\Modules\Ticket\Services\TicketService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TicketApiController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected TicketService $ticketService
    ) {}

    /**
     * List active ticket categories.
     */
    public function categories(): JsonResponse
    {
        $categories = TicketCategory::active()->get();
        return $this->success(
            TicketCategoryResource::collection($categories),
            'Ticket categories fetched successfully.'
        );
    }

    /**
     * List user's tickets.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = $user->tickets()->with(['category', 'assignee'])->orderBy('updated_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $tickets = $query->paginate($request->integer('per_page', 15));

        return $this->paginated(
            $tickets,
            TicketResource::class,
            'Tickets retrieved successfully.'
        );
    }

    /**
     * Create a new ticket.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subject'       => 'required|string|max:255',
            'category_id'   => 'nullable|exists:ticket_categories,id',
            'priority'      => ['nullable', Rule::enum(TicketPriority::class)],
            'message'       => 'required|string',
            'attachments'   => 'nullable|array',
            'attachments.*' => 'file|max:10240|mimes:jpeg,png,jpg,gif,pdf,doc,docx,zip,txt',
        ]);

        $ticket = $this->ticketService->createTicket(
            $request->user(),
            $validated,
            $request->file('attachments', [])
        );

        return $this->created(
            new TicketDetailResource($ticket),
            'Ticket submitted successfully.'
        );
    }

    /**
     * Show single ticket details with thread.
     */
    public function show(Request $request, Ticket $ticket): JsonResponse
    {
        if ($ticket->user_id !== $request->user()->id && !$request->user()->hasRole(['Super Admin', 'Admin', 'Staff'])) {
            return $this->forbidden('Unauthorized to view this ticket.');
        }

        $ticket->load(['category', 'user', 'assignee', 'attachments', 'replies.user', 'replies.attachments']);

        return $this->success(
            new TicketDetailResource($ticket),
            'Ticket details retrieved successfully.'
        );
    }

    /**
     * Reply to a ticket.
     */
    public function reply(Request $request, Ticket $ticket): JsonResponse
    {
        if ($ticket->user_id !== $request->user()->id && !$request->user()->hasRole(['Super Admin', 'Admin', 'Staff'])) {
            return $this->forbidden('Unauthorized to reply to this ticket.');
        }

        $validated = $request->validate([
            'message'       => 'required|string',
            'attachments'   => 'nullable|array',
            'attachments.*' => 'file|max:10240|mimes:jpeg,png,jpg,gif,pdf,doc,docx,zip,txt',
        ]);

        $reply = $this->ticketService->replyToTicket(
            $ticket,
            $request->user(),
            $validated['message'],
            false,
            $request->file('attachments', [])
        );

        return $this->created(
            new TicketReplyResource($reply),
            'Reply sent successfully.'
        );
    }

    /**
     * Close a ticket.
     */
    public function close(Request $request, Ticket $ticket): JsonResponse
    {
        if ($ticket->user_id !== $request->user()->id && !$request->user()->hasRole(['Super Admin', 'Admin', 'Staff'])) {
            return $this->forbidden('Unauthorized.');
        }

        $ticket = $this->ticketService->closeTicket($ticket, $request->user());

        return $this->success(
            new TicketResource($ticket),
            'Ticket closed successfully.'
        );
    }
}
