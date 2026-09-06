<?php

declare(strict_types=1);

namespace App\Modules\Ticket\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Ticket\Enums\TicketPriority;
use App\Modules\Ticket\Enums\TicketStatus;
use App\Modules\Ticket\Models\CannedResponse;
use App\Modules\Ticket\Models\Ticket;
use App\Modules\Ticket\Models\TicketCategory;
use App\Modules\Ticket\Services\TicketService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TicketAdminController extends Controller
{
    public function __construct(
        protected TicketService $ticketService
    ) {}

    public function index(Request $request): View
    {
        $query = Ticket::with(['user', 'category', 'assignee'])->orderBy('updated_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $tickets = $query->paginate(20)->withQueryString();
        $categories = TicketCategory::all();
        $staffMembers = User::role(['Super Admin', 'Admin', 'Staff'])->get();

        $stats = [
            'total'       => Ticket::count(),
            'open'        => Ticket::where('status', TicketStatus::OPEN->value)->count(),
            'in_progress' => Ticket::where('status', TicketStatus::IN_PROGRESS->value)->count(),
            'resolved'    => Ticket::where('status', TicketStatus::RESOLVED->value)->count(),
        ];

        return view('Ticket::backend.tickets.index', compact('tickets', 'categories', 'staffMembers', 'stats'));
    }

    public function show(Ticket $ticket): View
    {
        $ticket->load(['user', 'category', 'assignee', 'attachments', 'replies.user', 'replies.attachments']);
        $staffMembers = User::role(['Super Admin', 'Admin', 'Staff'])->get();
        $cannedResponses = CannedResponse::active()->get();

        return view('Ticket::backend.tickets.show', compact('ticket', 'staffMembers', 'cannedResponses'));
    }

    public function reply(Request $request, Ticket $ticket): RedirectResponse
    {
        $validated = $request->validate([
            'message'          => 'required|string',
            'is_internal_note' => 'nullable|boolean',
            'attachments'      => 'nullable|array',
            'attachments.*'    => 'file|max:10240|mimes:jpeg,png,jpg,gif,pdf,doc,docx,zip,txt',
        ]);

        $this->ticketService->replyToTicket(
            $ticket,
            $request->user(),
            $validated['message'],
            $request->boolean('is_internal_note'),
            $request->file('attachments', [])
        );

        return back()->with('success', 'Reply posted successfully.');
    }

    public function assign(Request $request, Ticket $ticket): RedirectResponse
    {
        $request->validate([
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $staff = $request->assigned_to ? User::find($request->assigned_to) : null;
        $this->ticketService->assignTicket($ticket, $staff);

        return back()->with('success', 'Assignee updated successfully.');
    }

    public function updateStatus(Request $request, Ticket $ticket): RedirectResponse
    {
        $request->validate([
            'status' => ['required', Rule::enum(TicketStatus::class)],
        ]);

        $this->ticketService->updateStatus($ticket, TicketStatus::from($request->status));

        return back()->with('success', 'Status updated successfully.');
    }
}
