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
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">Posts</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0">Community Posts Management</h4>
        </div>
    </div>

    {{-- Status Notification Modal --}}
    <x-modal.status />

    {{-- Filter Alert Banner --}}
    @if($selectedUser)
        <div class="alert alert-info border-0 shadow-sm d-flex align-items-center justify-content-between mb-4 p-3">
            <div class="d-flex align-items-center">
                <i class="bi bi-person-circle fs-4 me-3 text-info"></i>
                <div>
                    <h6 class="fw-bold mb-0">Filtered by User: {{ $selectedUser->name }} ({{ $selectedUser->email }})</h6>
                    <small class="text-muted">Showing posts published by this specific user.</small>
                </div>
            </div>
            <a href="{{ route('admin.posts.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-x-lg me-1"></i> Clear User Filter
            </a>
        </div>
    @endif

    {{-- Main Card Table Container --}}
    <x-card title="{{ $selectedUser ? 'Posts by ' . $selectedUser->name : 'All System Posts (' . \App\Modules\Post\Models\Post::count() . ')' }}">
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
    </x-card>
</x-admin-layout>
