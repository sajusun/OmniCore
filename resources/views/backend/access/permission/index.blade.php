@extends('layouts.admin', ['title' => 'Permission Management'])

@section('content')

{{-- Page Header --}}
<div class="mb-8 flex items-center justify-between">
    <div>
        <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            <span>Access Control</span>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-indigo-600 dark:text-indigo-400 font-medium">Permissions</span>
        </nav>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Permission Management</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage individual permissions that can be mapped to
            roles.</p>
    </div>
    <a href="{{ route('admin.permissions.create') }}"
        class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-md shadow-indigo-200 dark:shadow-indigo-900/40 transition-all duration-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Add New Permission
    </a>
</div>

{{-- Alerts --}}
@if(session('success'))
<div
    class="mb-6 flex items-center gap-3 px-4 py-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 rounded-xl text-sm">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    {{ session('success') }}
</div>
@endif

{{-- Table --}}
<x-datatable id="permission-datatable" url="{{ route('admin.permissions.index') }}" :columns="[
        ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => '#', 'orderable' => false, 'searchable' => false],
        ['data' => 'name', 'name' => 'name', 'title' => 'Permission Name'],
        ['data' => 'guard_name', 'name' => 'guard_name', 'title' => 'Guard Name'],
        ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false],
    ]" />

{{-- Delete Confirmation Modal --}}
<div id="delete-modal-backdrop"
    class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm px-4">
    <div
        class="w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="p-6">
            <div class="flex items-start gap-4">
                <div
                    class="flex-shrink-0 w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Delete Permission?</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        This will permanently delete this permission. Users/roles will lose access mapping under this
                        key. This action cannot be undone.
                    </p>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button onclick="closeDeleteModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    Cancel
                </button>
                <button id="confirm-delete-btn"
                    class="px-4 py-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-lg shadow-sm transition-colors disabled:opacity-60">
                    Yes, Delete
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let deleteTargetId = null;

window.deletePermission = function(id) {
    deleteTargetId = id;
    document.getElementById('delete-modal-backdrop').classList.remove('hidden');
};

function closeDeleteModal() {
    deleteTargetId = null;
    document.getElementById('delete-modal-backdrop').classList.add('hidden');
}

document.getElementById('confirm-delete-btn').addEventListener('click', function () {
    if (!deleteTargetId) return;
    const btn = this;
    btn.disabled = true;
    btn.textContent = 'Deleting...';

    $.ajax({
        url: "{{ url('admin/permissions') }}/" + deleteTargetId,
        type: 'DELETE',
        data: { _token: "{{ csrf_token() }}" },
        success: function (response) {
            closeDeleteModal();
            if (response.status) {
                Swal.fire({ icon: 'success', title: 'Deleted!', text: response.message, timer: 2000, showConfirmButton: false });
                $('#permission-datatable').DataTable().ajax.reload();
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

document.getElementById('delete-modal-backdrop').addEventListener('click', function (e) {
    if (e.target === this) closeDeleteModal();
});
</script>
@endpush