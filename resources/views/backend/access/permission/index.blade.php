<x-admin-layout>
<x-slot name="title">Permission Management</x-slot>

<div class="container-fluid py-4">
    <x-page-header title="Permission Management" subtitle="Manage individual permissions that can be mapped to roles."
        :breadcrumbs="['Access Control' => null, 'Permissions' => null]">
        <x-slot:actions>
            <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2 px-3 shadow-sm">
                <i class="fas fa-plus"></i><span>Add New Permission</span>
            </a>
        </x-slot:actions>
    </x-page-header>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle"></i><span>{{ session('success') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="permission-table-wrapper">
        <x-datatable id="permission-datatable" url="{{ route('admin.permissions.index') }}" :columns="[
                ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => '#', 'orderable' => false, 'searchable' => false],
                ['data' => 'name', 'name' => 'name', 'title' => 'Permission Key'],
                ['data' => 'display_name', 'name' => 'display_name', 'title' => 'Display Name'],
                ['data' => 'guard_name', 'name' => 'guard_name', 'title' => 'Guard'],
                ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false],
            ]" />
    </div>
</div>

<x-modal.confirm-delete name="confirm-user-delete" action="#"
    message="Are you sure you want to delete this permission? All associated records will be permanently removed." />

<x-modal.status />

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
        background-color: var(--theme-table-header-bg, #f8fafc) !important;
        border-bottom: 1px solid var(--theme-card-border, #edf2f7) !important;
        color: var(--theme-muted-color, #475569) !important;
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
</x-admin-layout>