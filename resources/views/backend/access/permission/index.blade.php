@extends('layouts.admin', ['title' => 'Permission Management'])

@section('content')

{{-- Page Header --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <nav aria-label="breadcrumb" class="mb-1">
            <ol class="breadcrumb mb-0" style="font-size: 0.875rem;">
                <li class="breadcrumb-item text-muted flex-row align-items-center d-inline-flex gap-1">
                    <svg class="text-secondary" style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Access Control
                </li>
                <li class="breadcrumb-item active text-primary fw-medium" aria-current="page">Permissions</li>
            </ol>
        </nav>
        <h1 class="h3 mb-1 fw-bold text-dark">Permission Management</h1>
        <p class="small text-muted mb-0">Manage individual permissions that can be mapped to roles.</p>
    </div>
    <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2 px-3 py-2 shadow-sm" style="border-radius: 0.5rem;">
        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Add New Permission
    </a>
</div>

{{-- Alerts --}}
@if(session('success'))
<div class="alert alert-success d-flex align-items-center gap-2 border-0 shadow-sm mb-4" role="alert" style="border-radius: 0.5rem;">
    <svg class="flex-shrink-0" style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    <div>
        {{ session('success') }}
    </div>
</div>
@endif

{{-- Table --}}
<div class="card border-0 shadow-sm mb-4" style="border-radius: 0.5rem;">
    <div class="card-body p-3">
        <x-datatable id="permission-datatable" url="{{ route('admin.permissions.index') }}" :columns="[
                ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => '#', 'orderable' => false, 'searchable' => false],
                ['data' => 'name', 'name' => 'name', 'title' => 'Permission Name'],
                ['data' => 'guard_name', 'name' => 'guard_name', 'title' => 'Guard Name'],
                ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false],
            ]" />
    </div>
</div>

{{-- Delete Confirmation Modal (Bootstrap 5 Static Native Structure) --}}
<div class="modal fade" id="deletePermissionModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deletePermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 450px;">
        <div class="modal-content border-0 shadow" style="border-radius: 1rem;">
            <div class="modal-body p-4">
                <div class="d-flex align-items-start gap-3">
                    <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-danger-subtle text-danger rounded-circle" style="width: 3rem; height: 3rem;">
                        <svg style="width: 1.5rem; height: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1" id="deletePermissionModalLabel">Delete Permission?</h5>
                        <p class="text-muted small mb-0">
                            This will permanently delete this permission. Users/roles will lose access mapping under this key. This action cannot be undone.
                        </p>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal" style="border-radius: 0.375rem;">Cancel</button>
                    <button type="button" id="confirm-delete-btn" class="btn btn-danger px-4" style="border-radius: 0.375rem;">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

<x-modal.confirm-delete name="confirm-user-delete" action="#"
    message="Are you sure you want to delete this permission? All associated records will be permanently removed." />

<x-modal.status />

@endsection

@push('scripts')
<script>
    function deletePermission(id) {
    let url = "{{ route('admin.permissions.destroy', ':id') }}";
    url = url.replace(':id', id);
    const form = document.getElementById('confirm-delete-form-confirm-user-delete');
    form.action = url;
    const modalElement = document.getElementById('modal_confirm-user-delete');
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
}
</script>
@endpush