<x-admin-layout>
    <x-slot name="title">
        @isset($user)
        {{ $user->name }}'s Activity Logs
        @else
        Activity Logs
        @endisset
    </x-slot>

    <x-slot name="header">
        @isset($user)
        Activity Logs for {{ $user->name }}
        @else
        System Activity Logs
        @endisset
    </x-slot>

    <!-- Breadcrumbs & Nav -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                </li>
                @isset($user)
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.users.index') }}">Users</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Activity Logs</li>
                @else
                <li class="breadcrumb-item active" aria-current="page">Activity Logs</li>
                @endisset
            </ol>
        </nav>
    </div>

    <!-- Search & Filter Card -->
    <x-card class="mb-4" style="border-radius: 0;">
        <form method="GET"
            action="{{ isset($user) ? route('admin.users.activity-logs', $user) : route('admin.activity-logs.index') }}">
            <div class="row g-3">
                <div class="col-12 col-sm-6 col-md">
                    <label for="search" class="form-label fw-medium small">Search</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        placeholder="User, description, event..." class="form-control" style="border-radius: 0;" />
                </div>
                <div class="col-12 col-sm-6 col-md">
                    <label for="event" class="form-label fw-medium small">Event</label>
                    <select name="event" id="event" class="form-select" style="border-radius: 0;">
                        <option value="">All Events</option>
                        @foreach($events as $event)
                        <option value="{{ $event }}" @selected(request('event')==$event)>{{ ucfirst($event) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md">
                    <label for="module" class="form-label fw-medium small">Module</label>
                    <select name="module" id="module" class="form-select" style="border-radius: 0;">
                        <option value="">All Modules</option>
                        @foreach($modules as $module)
                        <option value="{{ $module }}" @selected(request('module')==$module)>{{ $module }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md">
                    <label for="date_from" class="form-label fw-medium small">Date From</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                        class="form-control" style="border-radius: 0;" />
                </div>
                <div class="col-12 col-sm-6 col-md">
                    <label for="date_to" class="form-label fw-medium small">Date To</label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                        class="form-control" style="border-radius: 0;" />
                </div>
            </div>

            <div class="mt-3 d-flex justify-content-end gap-2">
                <a href="{{ isset($user) ? route('admin.users.activity-logs', $user) : route('admin.activity-logs.index') }}"
                    class="btn btn-outline-secondary btn-sm" style="border-radius: 0;">
                    Clear Filters
                </a>
                <button type="submit" class="btn btn-primary btn-sm" style="border-radius: 0;">
                    Apply Filters
                </button>
            </div>
        </form>
    </x-card>

    <!-- Table List Card -->
    <x-card title="Logs List" :noPadding="true" class="mb-4" style="border-radius: 0;">
        <x-table>
            <x-slot name="thead">
                <x-table.th>ID</x-table.th>
                <x-table.th>User</x-table.th>
                <x-table.th>Event</x-table.th>
                <x-table.th>Module</x-table.th>
                <x-table.th>Description</x-table.th>
                <x-table.th>Created At</x-table.th>
                <x-table.th class="text-end">Action</x-table.th>
            </x-slot>

            @forelse($activityLogs as $log)
            <tr>
                <x-table.td class="fw-semibold">
                    #{{ $log->id }}
                </x-table.td>
                <x-table.td>
                    @if($log->user)
                    <div class="fw-medium">{{ $log->user->name }}</div>
                    <div class="text-muted small">{{ $log->user->email }}</div>
                    @else
                    <span class="text-muted fst-italic">System / Guest</span>
                    @endif
                </x-table.td>
                <x-table.td>
                    @php
                    $badgeClass = match($log->event) {
                    'created' => 'bg-success',
                    'updated' => 'bg-primary',
                    'deleted', 'force_deleted' => 'bg-danger',
                    'restored' => 'bg-warning text-dark',
                    'login' => 'bg-info text-dark',
                    'logout' => 'bg-secondary',
                    default => 'bg-light text-dark border',
                    };
                    @endphp
                    <span class="badge {{ $badgeClass }}" style="border-radius: 0;">
                        {{ ucfirst($log->event) }}
                    </span>
                </x-table.td>
                <x-table.td>
                    {{ $log->module }}
                </x-table.td>
                <x-table.td class="text-truncate" style="max-width: 250px;" title="{{ $log->description }}">
                    {{ $log->description }}
                </x-table.td>
                <x-table.td class="small">
                    {{ $log->created_at->format('Y-m-d H:i:s') }}
                    <div class="text-muted">{{ $log->created_at->diffForHumans() }}</div>
                </x-table.td>
                <x-table.td class="text-end">
                    <a href="{{ route('admin.activity-logs.show', $log) }}" class="btn btn-sm btn-outline-primary"
                        style="border-radius: 0;" title="View Activity Details">
                        <i class="fa fa-eye"></i>
                    </a>
                </x-table.td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-5 text-center text-muted">
                    <div class="d-flex flex-column align-items-center justify-content-center">
                        <i class="fa fa-receipt fs-3 mb-2 text-muted opacity-50"></i>
                        <span class="small">No activity logs found.</span>
                    </div>
                </td>
            </tr>
            @endforelse
        </x-table>

        <!-- Pagination Links -->
        @if($activityLogs->hasPages())
        <div class="p-3 border-top">
            {{ $activityLogs->links() }}
        </div>
        @endif
    </x-card>
</x-admin-layout>
