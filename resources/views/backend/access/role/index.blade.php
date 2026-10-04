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

{{-- View All Permissions Modal --}}
<div class="modal fade" id="permissionsModal" tabindex="-1" aria-labelledby="permissionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom py-3 px-4">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2 mb-0" id="permissionsModalLabel">
                    <i class="fas fa-shield-alt text-primary"></i>
                    <span>Permissions — <span id="modalRoleName" class="text-primary"></span></span>
                    <span class="badge bg-primary text-white rounded-0 ms-1" id="modalPermCount" style="font-size: 0.75rem; padding: 0.35em 0.65em;">0</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <input type="text" id="modalPermSearch" class="form-control form-control-sm" placeholder="Search permissions..." onkeyup="filterModalPermissions(this.value)">
                </div>
                <div id="modalPermissionsContainer" class="d-flex flex-wrap gap-1" style="max-height: 380px; overflow-y: auto;">
                    {{-- Badges rendered dynamically --}}
                </div>
            </div>
            <div class="modal-footer border-top py-2 px-4">
                <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

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

    function showPermissionsModal(btn) {
        const roleName = btn.getAttribute('data-role');
        const permissions = JSON.parse(btn.getAttribute('data-permissions'));

        document.getElementById('modalRoleName').textContent = roleName;
        document.getElementById('modalPermCount').textContent = permissions.length;

        const container = document.getElementById('modalPermissionsContainer');
        container.innerHTML = '';

        permissions.forEach(function(perm) {
            const badge = document.createElement('span');
            badge.className = 'bg-primary text-white fw-medium rounded-0 perm-badge-item';
            badge.style.fontSize = '0.75rem';
            badge.style.padding = '0.35em 0.65em';
            badge.textContent = perm;
            container.appendChild(badge);
        });

        const searchInput = document.getElementById('modalPermSearch');
        if (searchInput) searchInput.value = '';

        const modalElement = document.getElementById('permissionsModal');
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    }

    function filterModalPermissions(query) {
        query = query.toLowerCase().trim();
        const items = document.querySelectorAll('.perm-badge-item');
        items.forEach(function(item) {
            if (query === '' || item.textContent.toLowerCase().includes(query)) {
                item.style.display = 'inline-block';
            } else {
                item.style.display = 'none';
            }
        });
    }
</script>
@endpush