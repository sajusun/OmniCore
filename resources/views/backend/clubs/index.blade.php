<x-admin-layout>

    <x-slot name="title">Clubs Management</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Clubs Management</h2>
    </x-slot>

    {{-- Page Header / Breadcrumb Component Area --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.875rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"
                            class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">Clubs Management</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0">Clubs Table</h4>
        </div>
    </div>

    {{-- Filter Alert Banner --}}
    @if(isset($selectedUser) && $selectedUser)
        <div class="alert alert-info border-0 shadow-sm d-flex align-items-center justify-content-between mb-4 p-3" style="border-radius: 0;">
            <div class="d-flex align-items-center">
                <i class="fa fa-user-circle fs-4 me-3 text-info"></i>
                <div>
                    <h6 class="fw-bold mb-0">Filtered by User: {{ $selectedUser->name }} ({{ $selectedUser->email }})</h6>
                    <small class="text-muted">Showing clubs created by this specific user.</small>
                </div>
            </div>
            <a href="{{ route('admin.clubs.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fa fa-times me-1"></i> Clear User Filter
            </a>
        </div>
    @endif

    {{-- Main Card Table Container --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
        <div class="card-header bg-transparent border-0 pt-3 pb-0 px-3">
            <h5 class="card-title mb-0 fw-bold text-dark">
                {{ isset($selectedUser) && $selectedUser ? 'Clubs by ' . $selectedUser->name : 'All System Clubs' }}
            </h5>
        </div>
        <div class="card-body p-3">
            <!-- Reusable Datatable Component Integration -->
            <x-datatable id="club-datatable" :url="route('admin.clubs.index', request()->query())" :columns="[
                ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'SL', 'orderable' => false, 'searchable' => false],
                ['data' => 'name', 'name' => 'name', 'title' => 'Club Name'],
                ['data' => 'type', 'name' => 'type', 'title' => 'Type'],
                ['data' => 'creator', 'name' => 'creator.name', 'title' => 'Creator User'],
                ['data' => 'location', 'name' => 'city', 'title' => 'Location'],
                ['data' => 'status', 'name' => 'status', 'title' => 'Status'],
                ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false]
            ]" />
        </div>
    </div>

    <x-modal.confirm-delete name="confirm-club-delete" action="#"
        message="Are you sure you want to delete this club? All associated records will be permanently removed." />

    @push('scripts')
    <script>
        function confirmDeleteClub(id) {
            let url = "{{ route('admin.clubs.destroy', ':id') }}";
            url = url.replace(':id', id);
            const form = document.getElementById('confirm-delete-form-confirm-club-delete');
            if (form) form.action = url;
            const modalElement = document.getElementById('modal_confirm-club-delete');
            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            }
        }
    </script>
    @endpush
</x-admin-layout>
