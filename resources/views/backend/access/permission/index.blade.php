@extends('layouts.admin', ['title' => 'Permission Management'])

@section('content')

{{-- Page Header --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <nav aria-label="breadcrumb" class="mb-1">
            <ol class="breadcrumb mb-0" style="font-size: 0.875rem;">
                <li class="breadcrumb-item text-muted">
                    <i class="fas fa-shield-alt me-1 text-secondary"></i> Access Control
                </li>
                <li class="breadcrumb-item active text-primary fw-medium" aria-current="page">Permissions</li>
            </ol>
        </nav>
        <h1 class="h3 mb-1 fw-bold text-dark">Permission Management</h1>
        <p class="small text-muted mb-0">Manage individual permissions that can be mapped to roles.</p>
    </div>
    <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2 px-3 py-2 shadow-sm" style="border-radius: 0.5rem;">
        <i class="fas fa-plus"></i>
        <span>Add New Permission</span>
    </a>
</div>

{{-- Alerts --}}
@if(session('success'))
<div class="alert alert-success d-flex align-items-center gap-2 border-0 shadow-sm mb-4" role="alert" style="border-radius: 0.5rem;">
    <i class="fas fa-check-circle fs-5 flex-shrink-0 text-success"></i>
    <div>
        {{ session('success') }}
    </div>
</div>
@endif

{{-- Table --}}
<div class="permission-table-wrapper mb-4">
    <x-datatable id="permission-datatable" url="{{ route('admin.permissions.index') }}" :columns="[
            ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => '#', 'orderable' => false, 'searchable' => false],
            ['data' => 'name', 'name' => 'name', 'title' => 'Permission Name'],
            ['data' => 'guard_name', 'name' => 'guard_name', 'title' => 'Guard Name'],
            ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false],
        ]" />
</div>

<x-modal.confirm-delete name="confirm-user-delete" action="#"
    message="Are you sure you want to delete this permission? All associated records will be permanently removed." />

<x-modal.status />

@endsection

@push('styles')
<style>
    .permission-table-wrapper .card {
        border-radius: 0.5rem;
    }
    .permission-table-wrapper .table-responsive {
        padding: 0 !important;
    }
    #permission-datatable {
        margin-bottom: 0 !important;
    }
    #permission-datatable thead th {
        padding: 0.65rem 1rem !important;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        letter-spacing: 0.05em !important;
        text-transform: uppercase !important;
        background-color: #f8fafc !important;
        border-bottom: 1px solid #edf2f7 !important;
        color: #475569 !important;
    }
    #permission-datatable tbody td {
        padding: 0.45rem 1rem !important;
        vertical-align: middle !important;
        font-size: 0.875rem !important;
    }
    #permission-datatable thead th:first-child,
    #permission-datatable tbody td:first-child {
        padding-left: 1.25rem !important;
        width: 50px !important;
        color: #64748b;
    }
    #permission-datatable thead th:last-child,
    #permission-datatable tbody td:last-child {
        padding-right: 1.25rem !important;
        width: 100px !important;
    }
    #permission-datatable tbody tr {
        transition: background-color 0.15s ease;
    }
    #permission-datatable tbody tr:hover {
        background-color: rgba(99, 102, 241, 0.03) !important;
    }
</style>
@endpush

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