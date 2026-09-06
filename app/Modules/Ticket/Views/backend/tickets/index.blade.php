@extends('backend.layouts.app')

@section('title', 'Support Tickets')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <h1 class="h3 fw-bold text-gray-800 mb-1">
                <i class="fa fa-life-ring me-2 text-primary"></i> Support Tickets
            </h1>
            <p class="text-muted mb-0">Manage customer support tickets, inquiries, and incident reports.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.tickets.categories.index') }}" class="btn btn-outline-secondary me-2">
                <i class="fa fa-tags me-1"></i> Categories
            </a>
            <a href="{{ route('admin.tickets.canned-responses.index') }}" class="btn btn-outline-info">
                <i class="fa fa-comments me-1"></i> Canned Responses
            </a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary-subtle text-primary p-3 me-3">
                        <i class="fa fa-ticket fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Total Tickets</div>
                        <div class="fs-4 fw-bold text-dark">{{ number_format($stats['total']) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-info-subtle text-info p-3 me-3">
                        <i class="fa fa-folder-open fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Open</div>
                        <div class="fs-4 fw-bold text-info">{{ number_format($stats['open']) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-warning-subtle text-warning p-3 me-3">
                        <i class="fa fa-clock-o fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">In Progress</div>
                        <div class="fs-4 fw-bold text-warning">{{ number_format($stats['in_progress']) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-success-subtle text-success p-3 me-3">
                        <i class="fa fa-check-circle fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Resolved</div>
                        <div class="fs-4 fw-bold text-success">{{ number_format($stats['resolved']) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.tickets.index') }}" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control rounded-3" placeholder="Search ticket #, subject, customer..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select rounded-3">
                        <option value="">All Statuses</option>
                        @foreach(\App\Modules\Ticket\Enums\TicketStatus::cases() as $st)
                            <option value="{{ $st->value }}" {{ request('status') === $st->value ? 'selected' : '' }}>{{ $st->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="priority" class="form-select rounded-3">
                        <option value="">All Priorities</option>
                        @foreach(\App\Modules\Ticket\Enums\TicketPriority::cases() as $pr)
                            <option value="{{ $pr->value }}" {{ request('priority') === $pr->value ? 'selected' : '' }}>{{ $pr->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="category_id" class="form-select rounded-3">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-3">
                        <i class="fa fa-filter me-1"></i> Filter
                    </button>
                    @if(request()->hasAny(['search', 'status', 'priority', 'category_id', 'assigned_to']))
                        <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-secondary rounded-3">
                            <i class="fa fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Ticket</th>
                            <th>Customer</th>
                            <th>Category</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Assignee</th>
                            <th>Last Reply</th>
                            <th class="pe-4 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                            <tr>
                                <td class="ps-4">
                                    <a href="{{ route('admin.tickets.show', $ticket) }}" class="fw-bold text-decoration-none text-primary">
                                        {{ $ticket->ticket_number }}
                                    </a>
                                    <div class="text-dark fw-medium small">{{ Str::limit($ticket->subject, 40) }}</div>
                                </td>
                                <td>
                                    <div class="fw-semibold small">{{ $ticket->user->name ?? 'Deleted User' }}</div>
                                    <div class="text-muted small">{{ $ticket->user->email ?? '' }}</div>
                                </td>
                                <td>
                                    @if($ticket->category)
                                        <span class="badge bg-light text-dark border">{{ $ticket->category->name }}</span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $ticket->priority->badgeClass() }}">
                                        {{ $ticket->priority->label() }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $ticket->status->badgeClass() }}">
                                        {{ $ticket->status->label() }}
                                    </span>
                                </td>
                                <td>
                                    @if($ticket->assignee)
                                        <span class="badge bg-secondary-subtle text-secondary">{{ $ticket->assignee->name }}</span>
                                    @else
                                        <span class="text-muted small italic">Unassigned</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted small">
                                        {{ $ticket->last_reply_at ? $ticket->last_reply_at->diffForHumans() : $ticket->created_at->diffForHumans() }}
                                    </span>
                                </td>
                                <td class="pe-4 text-end">
                                    <a href="{{ route('admin.tickets.show', $ticket) }}" class="btn btn-sm btn-outline-primary rounded-3">
                                        <i class="fa fa-eye me-1"></i> View Thread
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fa fa-ticket fa-3x mb-3 text-secondary opacity-50 d-block"></i>
                                    No tickets found matching your criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($tickets->hasPages())
                <div class="p-3 border-top">
                    {{ $tickets->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
