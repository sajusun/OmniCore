<x-admin-layout>
    @slot('title')
        Support Tickets
    @endslot

    <div class="container-fluid py-4">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1"><i class="bi bi-headset me-2 text-primary"></i> Support Tickets</h4>
                <p class="text-muted small mb-0">Manage customer support inquiries, assign staff agents, and monitor resolutions.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.tickets.categories.index') }}" class="btn btn-outline-secondary px-3">
                    <i class="bi bi-tags me-1"></i> Categories
                </a>
                <a href="{{ route('admin.tickets.canned-responses.index') }}" class="btn btn-outline-primary px-3">
                    <i class="bi bi-chat-quote me-1"></i> Canned Responses
                </a>
            </div>
        </div>

        {{-- Status Notification Modal --}}
        <x-modal.status />

        {{-- Stats Row --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <x-stat-card 
                    title="Total Tickets" 
                    value="{{ number_format($stats['total']) }}" 
                    color="primary" 
                    icon="<i class='bi bi-ticket-detailed fs-3'></i>" 
                />
            </div>
            <div class="col-md-3">
                <x-stat-card 
                    title="Open Inquiries" 
                    value="{{ number_format($stats['open']) }}" 
                    color="info" 
                    icon="<i class='bi bi-envelope-open fs-3'></i>" 
                />
            </div>
            <div class="col-md-3">
                <x-stat-card 
                    title="In Progress" 
                    value="{{ number_format($stats['in_progress']) }}" 
                    color="warning" 
                    icon="<i class='bi bi-hourglass-split fs-3'></i>" 
                />
            </div>
            <div class="col-md-3">
                <x-stat-card 
                    title="Resolved" 
                    value="{{ number_format($stats['resolved']) }}" 
                    color="emerald" 
                    icon="<i class='bi bi-check2-all fs-3'></i>" 
                />
            </div>
        </div>

        {{-- Filter & Search Card --}}
        <x-card title="Search & Filter Tickets" class="mb-4">
            <form method="GET" action="{{ route('admin.tickets.index') }}" class="row g-3 align-items-center">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search ticket #, subject, customer name..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        @foreach(\App\Modules\Ticket\Enums\TicketStatus::cases() as $st)
                            <option value="{{ $st->value }}" {{ request('status') === $st->value ? 'selected' : '' }}>
                                {{ $st->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="priority" class="form-select">
                        <option value="">All Priorities</option>
                        @foreach(\App\Modules\Ticket\Enums\TicketPriority::cases() as $pr)
                            <option value="{{ $pr->value }}" {{ request('priority') === $pr->value ? 'selected' : '' }}>
                                {{ $pr->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-3 w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
                    <a href="{{ route('admin.tickets.index') }}" class="btn btn-light border px-3">Reset</a>
                </div>
            </form>
        </x-card>

        {{-- Tickets Table --}}
        <x-card title="Tickets Directory">
            <x-table>
                <thead>
                    <tr>
                        <x-table.th>Ticket #</x-table.th>
                        <x-table.th>Customer</x-table.th>
                        <x-table.th>Subject</x-table.th>
                        <x-table.th>Priority</x-table.th>
                        <x-table.th>Status</x-table.th>
                        <x-table.th>Assigned To</x-table.th>
                        <x-table.th>Created</x-table.th>
                        <x-table.th class="text-end">Action</x-table.th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                        <tr>
                            <x-table.td>
                                <a href="{{ route('admin.tickets.show', $ticket) }}" class="fw-bold text-primary font-monospace text-decoration-none">
                                    {{ $ticket->ticket_number }}
                                </a>
                            </x-table.td>
                            <x-table.td>
                                <div class="fw-bold text-dark">{{ $ticket->user?->name ?? 'Guest / Deleted' }}</div>
                                <div class="text-muted small">{{ $ticket->user?->email }}</div>
                            </x-table.td>
                            <x-table.td>
                                <div class="fw-semibold text-dark text-truncate" style="max-width: 260px;">{{ $ticket->subject }}</div>
                                @if($ticket->category)
                                    <span class="badge bg-light text-dark border small mt-1">{{ $ticket->category->name }}</span>
                                @endif
                            </x-table.td>
                            <x-table.td>
                                @php
                                    $prColor = match($ticket->priority?->value) {
                                        'critical' => 'danger',
                                        'high' => 'danger',
                                        'medium' => 'warning',
                                        default => 'secondary'
                                    };
                                @endphp
                                <x-badge :color="$prColor">{{ $ticket->priority?->label() ?? 'Normal' }}</x-badge>
                            </x-table.td>
                            <x-table.td>
                                @php
                                    $stColor = match($ticket->status?->value) {
                                        'open' => 'info',
                                        'in_progress' => 'warning',
                                        'resolved' => 'success',
                                        'closed' => 'dark',
                                        default => 'secondary'
                                    };
                                @endphp
                                <x-badge :color="$stColor">{{ $ticket->status?->label() ?? 'Open' }}</x-badge>
                            </x-table.td>
                            <x-table.td class="text-muted small">
                                {{ $ticket->assignee?->name ?? 'Unassigned' }}
                            </x-table.td>
                            <x-table.td class="text-muted small">
                                {{ $ticket->created_at->format('M d, Y') }}
                            </x-table.td>
                            <x-table.td class="text-end">
                                <a href="{{ route('admin.tickets.show', $ticket) }}" class="btn btn-sm btn-primary px-3">
                                    <i class="bi bi-chat-dots me-1"></i> View Thread
                                </a>
                            </x-table.td>
                        </tr>
                    @empty
                        <x-empty-state colspan="8" title="No Support Tickets Found" description="No customer tickets match your criteria." />
                    @endforelse
                </tbody>
            </x-table>

            <div class="mt-3">
                {{ $tickets->links() }}
            </div>
        </x-card>
    </div>
</x-admin-layout>
