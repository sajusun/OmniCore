<x-admin-layout>
    <x-slot name="title">Staff Management</x-slot>

    <div class="container-fluid py-4">
        <x-page-header title="Staff Management" subtitle="Manage system administrators, staff members and their roles." :breadcrumbs="['Dashboard' => route('admin.dashboard'), 'Staff' => null]">
            <x-slot:actions>
                <a href="{{ route('admin.stuff.create') }}"
                   class="btn btn-primary d-inline-flex align-items-center gap-2 px-3.5 py-2 shadow-sm fw-semibold"
                   style="font-size: 0.8125rem; border-radius: 8px;">
                    <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <span>Add New Staff</span>
                </a>
            </x-slot:actions>
        </x-page-header>

    <x-card title="Users List" class="mb-6">
        <!-- Using the Reusable x-datatable Component -->
        <x-datatable id="user-datatable" :url="route('admin.stuff.index')" :columns="[
            ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'SL', 'orderable' => false, 'searchable' => false],
            ['data' => 'name', 'name' => 'name', 'title' => 'Name'],
            ['data' => 'email', 'name' => 'email', 'title' => 'Email'],
            ['data' => 'role', 'name' => 'role', 'title' => 'Role'],
            ['data' => 'status', 'name' => 'status', 'title' => 'Status'],
            ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false]
        ]" />
    </x-card>

    </div>

    {{-- Reusable Delete Confirmation Modal --}}
    <x-modal.confirm-delete name="confirm-user-delete" action=""
        message="Are you sure you want to delete this User? All associated records will be permanently removed." />

    {{-- Reusable Success/Error Toast status modal --}}
    <x-modal.status />

    @push('scripts')
    <script>
        function deleteAdmin(id, name) {
    let url = "{{ route('admin.stuff.destroy', ':id') }}";
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