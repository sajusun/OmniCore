<x-admin-layout>
    <x-slot name="title">Support Tickets</x-slot>

    <div class="container-fluid py-4">
        <x-page-header
            title="Support Tickets"
            subtitle="Manage and respond to user queries, issues, and support tickets."
            :breadcrumbs="['Dashboard' => route('admin.dashboard'), 'Support Tickets' => null]">
        </x-page-header>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <x-card title="All Tickets" :noPadding="true" class="mb-4">
            <x-slot:icon>
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                </svg>
            </x-slot:icon>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-4" style="width: 80px;">ID</th>
                            <th scope="col">User</th>
                            <th scope="col">Subject</th>
                            <th scope="col">Priority</th>
                            <th scope="col">Status</th>
                            <th scope="col">Created Date</th>
                            <th scope="col" class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                        <tr>
                            <td class="ps-4 fw-semibold text-muted">#{{ $ticket->id }}</td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $ticket->user->name ?? 'Unknown' }}</span>
                            </td>
                            <td>
                                <span class="text-dark">{{ $ticket->subject }}</span>
                            </td>
                            <td>
                                @php
                                    $pClass = match(strtolower($ticket->priority ?? '')) {
                                        'high'   => 'bg-danger text-white',
                                        'medium' => 'bg-warning text-dark',
                                        default  => 'bg-info text-white',
                                    };
                                @endphp
                                <span class="badge {{ $pClass }} px-2.5 py-1">
                                    {{ ucfirst($ticket->priority) }}
                                </span>
                            </td>
                            <td>
                                @if(strtolower($ticket->status ?? '') === 'open')
                                    <span class="badge bg-success text-white px-2.5 py-1">Open</span>
                                @else
                                    <span class="badge bg-secondary text-white px-2.5 py-1">Closed</span>
                                @endif
                            </td>
                            <td class="text-muted small">
                                {{ $ticket->created_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('admin.tickets.show', $ticket->id) }}" class="btn btn-sm btn-outline-primary px-3 py-1">
                                    <i class="fa fa-eye me-1"></i> View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fa fa-inbox fa-2x mb-2 d-block opacity-50"></i>
                                No support tickets found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($tickets, 'hasPages') && $tickets->hasPages())
                <div class="p-3 border-top">
                    {{ $tickets->links() }}
                </div>
            @endif
        </x-card>
    </div>
</x-admin-layout>
