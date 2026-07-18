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
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary px-3 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2">
            <i class="fas fa-plus"></i>
            Add New Role
        </a>
    </div>
</div>

{{-- Success Message Alert --}}
@if(session('t-success'))
<div class="alert alert-success d-flex align-items-center gap-2 rounded-3 mb-4" role="alert">
    <i class="fas fa-check-circle flex-shrink-0"></i>
    <div>
        {{ session('t-success') }}
    </div>
</div>
@endif

{{-- Datatable Section --}}
<div class="card border-0 shadow-sm rounded-3 p-3">
    <x-datatable
        id="role-datatable"
        url="{{ route('admin.roles.index') }}"
        :columns="[
            ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => '#', 'orderable' => false, 'searchable' => false],
            ['data' => 'name', 'name' => 'name', 'title' => 'Role Name'],
            ['data' => 'permissions', 'name' => 'permissions', 'title' => 'Permissions', 'orderable' => false],
            ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false],
        ]"
    />
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteRoleModal" tabindex="-1" aria-labelledby="deleteRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-body p-4">
                <div class="d-flex align-items-start gap-3">
                    <div class="flex-shrink-0 bg-danger-subtle text-danger rounded-circle d-flex align-items-center justify-center" style="width: 48px; height: 48px; font-size: 1.25rem;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-semibold text-dark mb-1" id="deleteRoleModalLabel">Delete Role?</h5>
                        <p class="text-muted small mb-0">
                            Deleting a role will affect all users assigned to it. This action cannot be undone.
                        </p>
                    </div>
                </div>
                <div class="mt-4 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light border px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirm-delete-btn" class="btn btn-danger px-3">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let deleteTargetId = null;
// Bootstrap Modal Instance তৈরি
const deleteModal = new bootstrap.Modal(document.getElementById('deleteRoleModal'));

window.deleteRole = function(id) {
    deleteTargetId = id;
    deleteModal.show();
};

function closeDeleteModal() {
    deleteTargetId = null;
    deleteModal.hide();
}

document.getElementById('confirm-delete-btn').addEventListener('click', function () {
    if (!deleteTargetId) return;
    const btn = this;
    btn.disabled = true;
    btn.textContent = 'Deleting...';

    $.ajax({
        url: "{{ url('admin/roles') }}/" + deleteTargetId,
        type: 'DELETE',
        data: { _token: "{{ csrf_token() }}" },
        success: function (response) {
            closeDeleteModal();
            if (response.status) {
                Swal.fire({ icon: 'success', title: 'Deleted!', text: response.message, timer: 2000, showConfirmButton: false });
                $('#role-datatable').DataTable().ajax.reload();
            } else {
                Swal.fire({ icon: 'error', title: 'Error!', text: response.message });
            }
            btn.disabled = false;
            btn.textContent = 'Yes, Delete';
        },
        error: function () {
            closeDeleteModal();
            Swal.fire({ icon: 'error', title: 'Error!', text: 'Something went wrong.' });
            btn.disabled = false;
            btn.textContent = 'Yes, Delete';
        }
    });
});
</script>
@endpush