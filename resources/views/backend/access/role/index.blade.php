<x-admin-layout>
<x-slot name="title">Role Management</x-slot>

<div class="container-fluid py-4">
    <x-page-header title="Role Management" subtitle="Manage roles and their associated permissions."
        :breadcrumbs="['Access Control' => null, 'Roles' => null]">
        <x-slot:actions>
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2 px-3 shadow-sm">
                <i class="fas fa-plus"></i><span>Add New Role</span>
            </a>
        </x-slot:actions>
    </x-page-header>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle"></i><span>{{ session('success') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

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
</x-admin-layout>