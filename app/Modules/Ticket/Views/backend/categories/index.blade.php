<x-admin-layout>
    @slot('title')
        Ticket Categories
    @endslot

    <div class="container-fluid py-4">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1"><i class="bi bi-tags me-2 text-primary"></i> Ticket Categories</h4>
                <p class="text-muted small mb-0">Organize support tickets into departmental and topic categories.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-secondary px-3">
                    <i class="bi bi-arrow-left me-1"></i> Back to Tickets
                </a>
                <button class="btn btn-primary px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                    <i class="bi bi-plus-circle me-1"></i> Add Category
                </button>
            </div>
        </div>

        {{-- Status Notification Modal --}}
        <x-modal.status />

        {{-- Categories Card --}}
        <x-card title="All Ticket Categories">
            <x-table>
                <thead>
                    <tr>
                        <x-table.th>Name</x-table.th>
                        <x-table.th>Slug</x-table.th>
                        <x-table.th>Description</x-table.th>
                        <x-table.th>Tickets Count</x-table.th>
                        <x-table.th>Status</x-table.th>
                        <x-table.th class="text-end">Action</x-table.th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                        <tr>
                            <x-table.td class="fw-bold text-dark">
                                @if($cat->icon)
                                    <i class="{{ $cat->icon }} me-2 text-primary"></i>
                                @endif
                                {{ $cat->name }}
                            </x-table.td>
                            <x-table.td><code class="text-primary">{{ $cat->slug }}</code></x-table.td>
                            <x-table.td class="text-muted small">{{ Str::limit($cat->description, 50) ?? '—' }}</x-table.td>
                            <x-table.td><x-badge color="primary">{{ $cat->tickets_count ?? 0 }}</x-badge></x-table.td>
                            <x-table.td>
                                @if($cat->is_active)
                                    <x-badge color="success">Active</x-badge>
                                @else
                                    <x-badge color="secondary">Inactive</x-badge>
                                @endif
                            </x-table.td>
                            <x-table.td class="text-end">
                                <form method="POST" action="{{ route('admin.tickets.categories.destroy', $cat) }}" class="d-inline" onsubmit="return confirm('Delete this category?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </x-table.td>
                        </tr>
                    @empty
                        <x-empty-state colspan="6" title="No Categories Found" description="Create your first ticket category above." />
                    @endforelse
                </tbody>
            </x-table>
        </x-card>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createCategoryModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form method="POST" action="{{ route('admin.tickets.categories.store') }}">
                    @csrf
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold">Add Ticket Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="e.g. Billing, Technical Support">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Category purpose..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Icon Class</label>
                            <input type="text" name="icon" class="form-control" placeholder="fa fa-credit-card or bi bi-gear">
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="catActive" checked>
                            <label class="form-check-label fw-bold" for="catActive">Category is Active</label>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
