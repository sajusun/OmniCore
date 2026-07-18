<x-admin-layout>
    <x-slot name="title">Users Table</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Users Table</h2>
    </x-slot>

    <div class="mb-6 flex justify-between items-center">
        <div class="text-sm text-gray-500">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900 transition-colors">Dashboard</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900 font-medium">Users Table</span>
        </div>
        <a href="{{ route('admin.stuff.create') }}"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                </path>
            </svg>
            Add New User
        </a>
    </div>

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