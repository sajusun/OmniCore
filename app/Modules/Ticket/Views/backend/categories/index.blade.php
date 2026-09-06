@extends('backend.layouts.app')

@section('title', 'Ticket Categories')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <a href="{{ route('admin.tickets.index') }}" class="text-decoration-none text-muted small mb-2 d-inline-block">
                <i class="fa fa-arrow-left me-1"></i> Back to Tickets
            </a>
            <h1 class="h3 fw-bold text-gray-800 mb-0">
                <i class="fa fa-tags me-2 text-primary"></i> Ticket Categories
            </h1>
        </div>
        <div class="col-auto">
            <button class="btn btn-primary rounded-3" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                <i class="fa fa-plus me-1"></i> Add Category
            </button>
        </div>
    </div>

    <!-- Categories List -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Name</th>
                            <th>Slug</th>
                            <th>Description</th>
                            <th>Tickets Count</th>
                            <th>Status</th>
                            <th class="pe-4 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $cat)
                            <tr>
                                <td class="ps-4 fw-bold text-dark">
                                    @if($cat->icon)
                                        <i class="{{ $cat->icon }} me-2 text-primary"></i>
                                    @endif
                                    {{ $cat->name }}
                                </td>
                                <td><code>{{ $cat->slug }}</code></td>
                                <td class="text-muted small">{{ Str::limit($cat->description, 50) ?? '—' }}</td>
                                <td><span class="badge bg-primary-subtle text-primary">{{ $cat->tickets_count }}</span></td>
                                <td>
                                    @if($cat->is_active)
                                        <span class="badge bg-success-subtle text-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="pe-4 text-end">
                                    <form method="POST" action="{{ route('admin.tickets.categories.destroy', $cat) }}" class="d-inline" onsubmit="return confirm('Delete this category?');">
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
                                <td colspan="6" class="text-center py-5 text-muted">No categories created yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create Category Modal -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">New Ticket Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.tickets.categories.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Name</label>
                        <input type="text" name="name" class="form-control rounded-3" required placeholder="e.g. Billing & Payments">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Icon (FontAwesome)</label>
                        <input type="text" name="icon" class="form-control rounded-3" placeholder="e.g. fa fa-credit-card">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control rounded-3" rows="3" placeholder="Category purpose..."></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active_check" checked>
                        <label class="form-check-label fw-semibold" for="is_active_check">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-3">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
