@extends('backend.app')

@section('title', "Ticket #{$ticket->ticket_number}")

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <a href="{{ route('admin.tickets.index') }}" class="text-decoration-none text-muted small mb-2 d-inline-block">
                <i class="fa fa-arrow-left me-1"></i> Back to Tickets
            </a>
            <div class="d-flex align-items-center gap-3">
                <h1 class="h3 fw-bold text-gray-800 mb-0">
                    #{{ $ticket->ticket_number }}: {{ $ticket->subject }}
                </h1>
                <span class="badge {{ $ticket->status->badgeClass() }} fs-6">
                    {{ $ticket->status->label() }}
                </span>
                <span class="badge {{ $ticket->priority->badgeClass() }} fs-6">
                    {{ $ticket->priority->label() }}
                </span>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Conversation Thread -->
        <div class="col-lg-8">
            <!-- Initial Ticket Message -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-light-subtle py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar-sm rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px;">
                            {{ strtoupper(substr($ticket->user->name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-bold text-dark">{{ $ticket->user->name ?? 'User' }} <span class="badge bg-secondary-subtle text-secondary ms-1">Customer</span></div>
                            <div class="text-muted small">{{ $ticket->created_at->format('M d, Y h:i A') }} ({{ $ticket->created_at->diffForHumans() }})</div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="text-dark fs-6" style="white-space: pre-wrap;">{{ $ticket->message }}</div>

                    @if($ticket->attachments->isNotEmpty())
                        <div class="mt-4 pt-3 border-top">
                            <h6 class="fw-semibold text-muted small mb-2">Attachments ({{ $ticket->attachments->count() }}):</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($ticket->attachments as $att)
                                    <a href="{{ $att->url }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-3">
                                        <i class="fa fa-paperclip me-1"></i> {{ $att->file_name }} ({{ number_format($att->file_size / 1024, 1) }} KB)
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Replies Thread -->
            @foreach($ticket->replies as $reply)
                <div class="card border-0 shadow-sm rounded-4 mb-4 {{ $reply->is_internal_note ? 'border-warning border-start border-4 bg-warning-subtle' : '' }}">
                    <div class="card-header bg-transparent py-3 border-bottom d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-sm rounded-circle {{ $reply->is_internal_note ? 'bg-warning text-dark' : ($reply->user_id === $ticket->user_id ? 'bg-primary text-white' : 'bg-success text-white') }} d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px;">
                                {{ strtoupper(substr($reply->user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-bold text-dark">
                                    {{ $reply->user->name ?? 'User' }}
                                    @if($reply->is_internal_note)
                                        <span class="badge bg-warning text-dark ms-1">Internal Note</span>
                                    @elseif($reply->user_id === $ticket->user_id)
                                        <span class="badge bg-primary-subtle text-primary ms-1">Customer</span>
                                    @else
                                        <span class="badge bg-success-subtle text-success ms-1">Support Staff</span>
                                    @endif
                                </div>
                                <div class="text-muted small">{{ $reply->created_at->format('M d, Y h:i A') }} ({{ $reply->created_at->diffForHumans() }})</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="text-dark fs-6" style="white-space: pre-wrap;">{{ $reply->message }}</div>

                        @if($reply->attachments->isNotEmpty())
                            <div class="mt-4 pt-3 border-top">
                                <h6 class="fw-semibold text-muted small mb-2">Attachments ({{ $reply->attachments->count() }}):</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($reply->attachments as $att)
                                        <a href="{{ $att->url }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-3">
                                            <i class="fa fa-paperclip me-1"></i> {{ $att->file_name }} ({{ number_format($att->file_size / 1024, 1) }} KB)
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

            <!-- Reply Form Card -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fa fa-reply me-2 text-primary"></i> Post a Reply
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.tickets.reply', $ticket) }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Canned Responses Helper -->
                        @if($cannedResponses->isNotEmpty())
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-muted">Insert Canned Template:</label>
                                <select class="form-select form-select-sm rounded-3" id="cannedResponseSelect" onchange="insertCannedResponse(this.value)">
                                    <option value="">-- Choose Template --</option>
                                    @foreach($cannedResponses as $cr)
                                        <option value="{{ e($cr->body) }}">{{ $cr->title }} ({{ $cr->shortcut ?? 'No shortcut' }})</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="mb-3">
                            <textarea name="message" id="replyMessage" rows="5" class="form-control rounded-3" placeholder="Type your response to the customer here..." required></textarea>
                        </div>

                        <div class="row g-3 align-items-center">
                            <div class="col-md-6">
                                <input type="file" name="attachments[]" multiple class="form-control form-control-sm rounded-3">
                                <div class="form-text small">Optional attachments (images, PDF, documents up to 10MB each)</div>
                            </div>
                            <div class="col-md-6 text-end d-flex justify-content-end align-items-center gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_internal_note" value="1" id="internalNoteCheck">
                                    <label class="form-check-label small fw-semibold text-warning" for="internalNoteCheck">
                                        <i class="fa fa-lock me-1"></i> Private Internal Note
                                    </label>
                                </div>
                                <button type="submit" class="btn btn-primary rounded-3 px-4">
                                    <i class="fa fa-paper-plane me-1"></i> Send Reply
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Actions & Meta -->
        <div class="col-lg-4">
            <!-- Ticket Controls -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold mb-0 text-dark">Ticket Actions</h5>
                </div>
                <div class="card-body p-4">
                    <!-- Status Form -->
                    <form method="POST" action="{{ route('admin.tickets.status', $ticket) }}" class="mb-4">
                        @csrf
                        @method('PATCH')
                        <label class="form-label fw-semibold small text-muted">Update Status</label>
                        <div class="input-group">
                            <select name="status" class="form-select rounded-start-3">
                                @foreach(\App\Modules\Ticket\Enums\TicketStatus::cases() as $st)
                                    <option value="{{ $st->value }}" {{ $ticket->status === $st ? 'selected' : '' }}>{{ $st->label() }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-primary" type="submit">Update</button>
                        </div>
                    </form>

                    <!-- Assignee Form -->
                    <form method="POST" action="{{ route('admin.tickets.assign', $ticket) }}">
                        @csrf
                        @method('PATCH')
                        <label class="form-label fw-semibold small text-muted">Assigned Staff</label>
                        <div class="input-group">
                            <select name="assigned_to" class="form-select rounded-start-3">
                                <option value="">-- Unassigned --</option>
                                @foreach($staffMembers as $staff)
                                    <option value="{{ $staff->id }}" {{ $ticket->assigned_to === $staff->id ? 'selected' : '' }}>{{ $staff->name }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-info text-white" type="submit">Assign</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Customer Meta -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold mb-0 text-dark">Customer Details</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <div class="text-muted small">Name</div>
                        <div class="fw-semibold text-dark">{{ $ticket->user->name ?? 'Deleted User' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Email</div>
                        <div class="fw-semibold text-dark">{{ $ticket->user->email ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Category</div>
                        <div class="fw-semibold text-dark">{{ $ticket->category->name ?? 'General' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Created At</div>
                        <div class="fw-semibold text-dark">{{ $ticket->created_at->format('M d, Y h:i A') }}</div>
                    </div>
                    @if($ticket->resolved_at)
                        <div class="mb-3">
                            <div class="text-muted small">Resolved At</div>
                            <div class="fw-semibold text-success">{{ $ticket->resolved_at->format('M d, Y h:i A') }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function insertCannedResponse(body) {
    if (!body) return;
    const textarea = document.getElementById('replyMessage');
    if (textarea.value.trim() !== '') {
        textarea.value += '\n\n' + body;
    } else {
        textarea.value = body;
    }
}
</script>
@endsection
