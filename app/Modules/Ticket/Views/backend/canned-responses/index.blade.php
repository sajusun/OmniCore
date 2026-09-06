@extends('backend.layouts.app')

@section('title', 'Canned Responses')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <a href="{{ route('admin.tickets.index') }}" class="text-decoration-none text-muted small mb-2 d-inline-block">
                <i class="fa fa-arrow-left me-1"></i> Back to Tickets
            </a>
            <h1 class="h3 fw-bold text-gray-800 mb-0">
                <i class="fa fa-comments me-2 text-primary"></i> Canned Responses
            </h1>
        </div>
        <div class="col-auto">
            <button class="btn btn-primary rounded-3" data-bs-toggle="modal" data-bs-target="#createCannedModal">
                <i class="fa fa-plus me-1"></i> Add Template
            </button>
        </div>
    </div>

    <!-- Canned Responses List -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Title</th>
                            <th>Shortcut</th>
                            <th>Body Preview</th>
                            <th>Status</th>
                            <th class="pe-4 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cannedResponses as $cr)
                            <tr>
                                <td class="ps-4 fw-bold text-dark">{{ $cr->title }}</td>
                                <td><code>{{ $cr->shortcut ?? '—' }}</code></td>
                                <td class="text-muted small">{{ Str::limit($cr->body, 80) }}</td>
                                <td>
                                    @if($cr->is_active)
                                        <span class="badge bg-success-subtle text-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="pe-4 text-end">
                                    <form method="POST" action="{{ route('admin.tickets.canned-responses.destroy', $cr) }}" class="d-inline" onsubmit="return confirm('Delete this canned response?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger rounded-3" type="submit">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">No canned responses saved yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create Canned Response Modal -->
<div class="modal fade" id="createCannedModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">New Canned Response</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.tickets.canned-responses.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Title</label>
                        <input type="text" name="title" class="form-control rounded-3" required placeholder="e.g. Password Reset Instructions">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Shortcut / Keyword</label>
                        <input type="text" name="shortcut" class="form-control rounded-3" placeholder="e.g. !reset_pw">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Message Body</label>
                        <textarea name="body" class="form-control rounded-3" rows="5" required placeholder="Predefined message template to paste into replies..."></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active_cr_check" checked>
                        <label class="form-check-label fw-semibold" for="is_active_cr_check">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-3">Save Template</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
