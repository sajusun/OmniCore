<?php

declare(strict_types=1);

namespace App\Modules\Ticket\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Ticket\Http\Requests\ReplyTicketRequest;
use App\Modules\Ticket\Http\Requests\StoreTicketRequest;
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
        $categories = \Illuminate\Support\Facades\Cache::remember('ticket_categories_active', 3600, function () {
            return TicketCategory::active()->get();
        });

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
    public function store(StoreTicketRequest $request): JsonResponse
    {
        $ticket = $this->ticketService->createTicket(
            $request->user(),
            $request->validated(),
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
        if ($ticket->user_id !== $request->user()->id && !$request->user()->hasAnyRole(['super_admin', 'admin', 'staff', 'Super Admin', 'Admin', 'Staff'])) {
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
    public function reply(ReplyTicketRequest $request, Ticket $ticket): JsonResponse
    {
        if ($ticket->user_id !== $request->user()->id && !$request->user()->hasAnyRole(['super_admin', 'admin', 'staff', 'Super Admin', 'Admin', 'Staff'])) {
            return $this->forbidden('Unauthorized to reply to this ticket.');
        }

        $validated = $request->validated();

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
        if ($ticket->user_id !== $request->user()->id && !$request->user()->hasAnyRole(['super_admin', 'admin', 'staff', 'Super Admin', 'Admin', 'Staff'])) {
            return $this->forbidden('Unauthorized.');
        }

        $ticket = $this->ticketService->closeTicket($ticket, $request->user());

        return $this->success(
            new TicketResource($ticket),
            'Ticket closed successfully.'
        );
    }
}
