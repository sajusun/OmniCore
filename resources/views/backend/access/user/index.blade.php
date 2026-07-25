<x-admin-layout>

    <x-slot name="title">Users Table</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Users Table</h2>
    </x-slot>

    {{-- Page Header / Breadcrumb Component Area --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.875rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"
                            class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">Users Table</li>
                </ol>
            </nav>
            {{-- <h2 class="h4 fw-bold text-dark mb-0">Users Table</h2> --}}
        </div>
        <a href="{{ route('admin.users.create') }}"
            class="btn btn-primary d-inline-flex align-items-center gap-2 px-3 py-2 shadow-sm text-uppercase fw-semibold"
            style="font-size: 0.75rem; letter-spacing: 0.05em; border-radius: 0;">
            <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                </path>
            </svg>
            Add New User
        </a>
    </div>

    {{-- Main Card Table Container --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
        <div class="card-header bg-transparent border-0 pt-3 pb-0 px-3">
            <h5 class="card-title mb-0 fw-bold text-dark">Users Table</h5>
        </div>
        <div class="card-body p-3">
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