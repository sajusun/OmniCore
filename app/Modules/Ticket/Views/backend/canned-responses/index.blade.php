<x-admin-layout>
    @slot('title')
        Canned Responses
    @endslot

    <div class="container-fluid py-4">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1"><i class="bi bi-chat-quote me-2 text-primary"></i> Canned Responses & Quick Replies</h4>
                <p class="text-muted small mb-0">Manage pre-written reply templates to accelerate support ticket response times.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-secondary px-3">
                    <i class="bi bi-arrow-left me-1"></i> Back to Tickets
                </a>
                <button class="btn btn-primary px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#createCannedModal">
                    <i class="bi bi-plus-circle me-1"></i> Add Template
                </button>
            </div>
        </div>

        {{-- Status Notification Modal --}}
        <x-modal.status />

        {{-- Canned Responses Card --}}
        <x-card title="Response Templates">
            <x-table>
                <thead>
                    <tr>
                        <x-table.th>Title</x-table.th>
                        <x-table.th>Shortcut</x-table.th>
                        <x-table.th>Body Preview</x-table.th>
                        <x-table.th>Status</x-table.th>
                        <x-table.th class="text-end">Action</x-table.th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cannedResponses as $cr)
                        <tr>
                            <x-table.td class="fw-bold text-dark">{{ $cr->title }}</x-table.td>
                            <x-table.td><code class="text-primary">{{ $cr->shortcut ?? '—' }}</code></x-table.td>
                            <x-table.td class="text-muted small">{{ Str::limit($cr->body, 80) }}</x-table.td>
                            <x-table.td>
                                @if($cr->is_active)
                                    <x-badge color="success">Active</x-badge>
                                @else
                                    <x-badge color="secondary">Inactive</x-badge>
                                @endif
                            </x-table.td>
                            <x-table.td class="text-end">
                                <form method="POST" action="{{ route('admin.tickets.canned-responses.destroy', $cr) }}" class="d-inline" onsubmit="return confirm('Delete this response?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </x-table.td>
                        </tr>
                    @empty
                        <x-empty-state colspan="5" title="No Canned Responses" description="Add your first quick reply template above." />
                    @endforelse
                </tbody>
            </x-table>
        </x-card>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createCannedModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form method="POST" action="{{ route('admin.tickets.canned-responses.store') }}">
                    @csrf
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold">Add Canned Response</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required placeholder="e.g. Standard Greeting, Refund Policy">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Shortcut Key</label>
                            <input type="text" name="shortcut" class="form-control font-monospace" placeholder="e.g. !hello, !refund">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Response Body <span class="text-danger">*</span></label>
                            <textarea name="body" class="form-control" rows="4" required placeholder="Hi {user_name}, thank you for reaching out..."></textarea>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="crActive" checked>
                            <label class="form-check-label fw-bold" for="crActive">Template is Active</label>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Template</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
