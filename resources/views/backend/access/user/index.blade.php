<x-admin-layout>
    <x-slot name="title">Users Management</x-slot>

    <div class="container-fluid py-4">
        <x-page-header title="Users Management" :breadcrumbs="['Dashboard' => route('admin.dashboard'), 'Users' => null]">
            <x-slot:actions>
                <a href="{{ route('admin.users.create') }}"
                   class="btn btn-primary d-inline-flex align-items-center gap-2 px-3.5 py-2 shadow-sm fw-semibold"
                   style="font-size: 0.8125rem; border-radius: 8px;">
                    <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <span>Add New User</span>
                </a>
            </x-slot:actions>
        </x-page-header>

        {{-- Main Datatable Card Container --}}
        <x-card title="Users Directory" :noPadding="true" class="mb-4">
            <x-slot:icon>
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </x-slot:icon>

            <div class="p-3">
                <!-- Reusable Datatable Component Integration -->
                <x-datatable id="user-datatable" :url="route('admin.users.index')" :columns="[
                    ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'SL', 'orderable' => false, 'searchable' => false],
                    ['data' => 'name', 'name' => 'name', 'title' => 'Name'],
                    ['data' => 'email', 'name' => 'email', 'title' => 'Email'],
                    ['data' => 'role', 'name' => 'role', 'title' => 'Role'],
                    ['data' => 'status', 'name' => 'status', 'title' => 'Status'],
                    ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false]
                ]" />
            </div>
        </x-card>
    </div>

    <x-modal.confirm-delete name="confirm-user-delete" action="#"
        message="Are you sure you want to delete this user account? All associated records will be permanently removed." />

    <x-modal.status />

    @push('scripts')
    <script>
        function confirmDeleteUser(id) {
            let url = "{{ route('admin.users.destroy', ':id') }}";
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