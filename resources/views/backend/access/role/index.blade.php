@extends('layouts.admin', ['title' => 'Role Management'])

@section('content')

{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb" class="mb-1">
            <ol class="breadcrumb mb-0" style="font-size: 0.875rem;">
                <li class="breadcrumb-item text-muted">
                    <i class="fas fa-shield-alt me-1"></i> Access Control
                </li>
                <li class="breadcrumb-item active text-primary fw-medium" aria-current="page">Roles</li>
            </ol>
        </nav>
        <h1 class="h3 mb-1 font-weight-bold text-dark dark:text-light">Role Management</h1>
        <p class="text-muted small mb-0">Manage roles and their associated permissions.</p>
    </div>
    <div>
        <a href="{{ route('admin.roles.create') }}"
            class="btn btn-primary px-3 py-2 shadow-sm d-inline-flex align-items-center gap-2">
            <i class="fas fa-plus"></i>
            Add New Role
        </a>
    </div>
</div>

{{-- Datatable Section --}}
<div class="card border-0 shadow-sm p-3">
    <x-datatable id="role-datatable" url="{{ route('admin.roles.index') }}" :columns="[
            ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => '#', 'orderable' => false, 'searchable' => false],
            ['data' => 'name', 'name' => 'name', 'title' => 'Role Name'],
            ['data' => 'permissions', 'name' => 'permissions', 'title' => 'Permissions', 'orderable' => false],
            ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false],
        ]" />
</div>

<x-modal.confirm-delete name="confirm-user-delete" action="#"
    message="Are you sure you want to delete this role? All associated records will be permanently removed." />

<x-modal.status />

@endsection

@push('scripts')
<script>
    function deleteRole(id) {
    let url = "{{ route('admin.roles.destroy', ':id') }}";
    url = url.replace(':id', id);
    const form = document.getElementById('confirm-delete-form-confirm-user-delete');
    form.action = url;
    const modalElement = document.getElementById('modal_confirm-user-delete');
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
}
</script>
@endpush