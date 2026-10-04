<x-admin-layout>
    <x-slot name="title">Ticket #{{ $ticket->id }} — {{ $ticket->subject }}</x-slot>

    <div class="container-fluid py-4">
        <x-page-header
            :title="'#' . $ticket->id . ' — ' . $ticket->subject"
            subtitle="View conversation and reply to this support ticket."
            :breadcrumbs="['Dashboard' => route('admin.dashboard'), 'Tickets' => route('admin.tickets.index'), '#' . $ticket->id => null]">
            <x-slot:actions>
                <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5 px-3 py-2" style="font-size: 0.8125rem; border-radius: 8px;">
                    <i class="fa fa-arrow-left"></i>
                    <span>Back to Tickets</span>
                </a>

                @if($ticket->status === 'open')
                    <form action="{{ route('admin.tickets.close', $ticket->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-danger d-inline-flex align-items-center gap-1.5 px-3 py-2 fw-semibold" style="font-size: 0.8125rem; border-radius: 8px;">
                            <i class="fa fa-times-circle"></i>
                            <span>Close Ticket</span>
                        </button>
                    </form>
                @else
                    <span class="badge bg-secondary px-3 py-2" style="font-size: 0.8125rem;">Closed</span>
                @endif
            </x-slot:actions>
        </x-page-header>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Ticket Conversation Stream --}}
        <div class="d-flex flex-column gap-3 mb-4">
            @forelse($ticket->messages as $msg)
                @php
                    $isSelf = $msg->user_id === Auth::id();
                @endphp
                <div class="d-flex {{ $isSelf ? 'justify-content-end' : 'justify-content-start' }}">
                    <div class="card border-0 shadow-sm {{ $isSelf ? 'bg-primary text-white' : 'bg-white text-dark' }}" style="max-width: 75%; border-radius: 12px;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between gap-3 mb-1 {{ $isSelf ? 'text-white-50' : 'text-muted' }} small" style="font-size: 0.75rem;">
                                <span class="fw-bold">{{ $msg->user->name ?? 'Unknown' }}</span>
                                <span>{{ $msg->created_at->diffForHumans() }}</span>
                            </div>
                            <div style="white-space: pre-wrap; font-size: 0.875rem;">{{ $msg->message }}</div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card border-0 shadow-sm text-center py-5 text-muted">
                    <p class="mb-0">No messages in this ticket yet.</p>
                </div>
            @endforelse
        </div>

        {{-- Reply Form Card --}}
        <x-card title="Send a Reply" class="mb-4">
            <x-slot:icon>
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                </svg>
            </x-slot:icon>

            <form action="{{ route('admin.tickets.reply', $ticket->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="message" class="form-label fw-medium small text-muted">Your Response</label>
                    <textarea name="message" id="message" rows="4" class="form-control" required placeholder="Type your reply here..."></textarea>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4 py-2 fw-semibold" style="font-size: 0.8125rem; border-radius: 8px;">
                        <i class="fa fa-paper-plane"></i>
                        <span>Send Reply</span>
                    </button>
                </div>
            </form>
        </x-card>
    </div>
</x-admin-layout>
