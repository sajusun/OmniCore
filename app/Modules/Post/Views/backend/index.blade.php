<x-admin-layout>

    <x-slot name="title">Posts Management</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Posts Management</h2>
    </x-slot>

    {{-- Page Header / Breadcrumb Component Area --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.875rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"
                            class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">Posts</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0">Posts List</h4>
        </div>
    </div>

    {{-- Filter Alert Banner --}}
    @if($selectedUser)
        <div class="alert alert-info border-0 shadow-sm d-flex align-items-center justify-content-between mb-4 p-3" style="border-radius: 0;">
            <div class="d-flex align-items-center">
                <i class="fa fa-user-circle fs-4 me-3 text-info"></i>
                <div>
                    <h6 class="fw-bold mb-0">Filtered by User: {{ $selectedUser->name }} ({{ $selectedUser->email }})</h6>
                    <small class="text-muted">Showing posts published by this specific user.</small>
                </div>
            </div>
            <a href="{{ route('admin.posts.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fa fa-times me-1"></i> Clear User Filter
            </a>
        </div>
    @endif

    {{-- Main Card Table Container --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
        <div class="card-header bg-transparent border-0 pt-3 pb-0 px-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-bold text-dark">
                {{ $selectedUser ? 'Posts by ' . $selectedUser->name : 'All System Posts' }}
            </h5>
        </div>
        <div class="card-body p-3">
            <!-- Reusable Datatable Component Integration -->
            <x-datatable id="post-datatable" :url="route('admin.posts.index', request()->query())" :order="[[6, 'desc']]" :columns="[
                ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'SL', 'orderable' => false, 'searchable' => false],
                ['data' => 'media', 'name' => 'media', 'title' => 'Media', 'orderable' => false, 'searchable' => false],
                ['data' => 'title', 'name' => 'title', 'title' => 'Title / Excerpt'],
                ['data' => 'user', 'name' => 'user.name', 'title' => 'User'],
                ['data' => 'type', 'name' => 'type', 'title' => 'Type'],
                ['data' => 'visibility', 'name' => 'visibility', 'title' => 'Visibility'],
                ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Date'],
                ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false]
            ]" />
        </div>
    </div>

    <x-modal.confirm-delete name="confirm-post-delete" action="#"
        message="Are you sure you want to delete this post? This action cannot be undone." />

    @push('scripts')
    <script>
        function confirmDeletePost(id) {
            let url = "{{ route('admin.posts.destroy', ':id') }}";
            url = url.replace(':id', id);
            const form = document.getElementById('confirm-delete-form-confirm-post-delete');
            if (form) form.action = url;
            const modalElement = document.getElementById('modal_confirm-post-delete');
            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            }
        }
    </script>
    @endpush
</x-admin-layout>
