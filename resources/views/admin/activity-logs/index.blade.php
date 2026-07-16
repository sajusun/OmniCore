<x-admin-layout>
    <x-slot name="title">
        @isset($user)
            {{ $user->name }}'s Activity Logs
        @else
            Activity Logs
        @endisset
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            @isset($user)
                Activity Logs for {{ $user->name }}
            @else
                System Activity Logs
            @endisset
        </h2>
    </x-slot>

    <!-- Breadcrumbs & Nav -->
    <div class="mb-6 flex justify-between items-center">
        <div class="text-sm text-gray-500 dark:text-gray-400">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900 dark:hover:text-gray-200 transition-colors">Dashboard</a>
            <span class="mx-2">/</span>
            @isset($user)
                <a href="{{ route('admin.users.index') }}" class="hover:text-gray-900 dark:hover:text-gray-200 transition-colors">Users</a>
                <span class="mx-2">/</span>
                <span class="text-gray-900 dark:text-gray-100 font-medium">Activity Logs</span>
            @else
                <span class="text-gray-900 dark:text-gray-100 font-medium">Activity Logs</span>
            @endisset
        </div>
    </div>

    <!-- Search & Filter Card -->
    <x-card class="mb-6">
        <form method="GET" action="{{ isset($user) ? route('admin.users.activity-logs', $user) : route('admin.activity-logs.index') }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="User, description, event..." 
                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" />
                </div>
                <div>
                    <label for="event" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Event</label>
                    <select name="event" id="event" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        <option value="">All Events</option>
                        @foreach($events as $event)
                            <option value="{{ $event }}" @selected(request('event') == $event)>{{ ucfirst($event) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="module" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Module</label>
                    <select name="module" id="module" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        <option value="">All Modules</option>
                        @foreach($modules as $module)
                            <option value="{{ $module }}" @selected(request('module') == $module)>{{ $module }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date From</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" 
                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" />
                </div>
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date To</label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" 
                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" />
                </div>
            </div>
            
            <div class="mt-4 flex justify-end gap-3">
                <a href="{{ isset($user) ? route('admin.users.activity-logs', $user) : route('admin.activity-logs.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150 ease-in-out">
                    Clear Filters
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                    Apply Filters
                </button>
            </div>
        </form>
    </x-card>

    <!-- Table List Card -->
    <x-card title="Logs List" :noPadding="true" class="mb-6">
        <x-table>
            <x-slot name="thead">
                <x-table.th>ID</x-table.th>
                <x-table.th>User</x-table.th>
                <x-table.th>Event</x-table.th>
                <x-table.th>Module</x-table.th>
                <x-table.th>Description</x-table.th>
                <x-table.th>Created At</x-table.th>
                <x-table.th class="text-right">Action</x-table.th>
            </x-slot>

            @forelse($activityLogs as $log)
                <tr>
                    <x-table.td class="font-semibold text-gray-900 dark:text-gray-100">
                        #{{ $log->id }}
                    </x-table.td>
                    <x-table.td>
                        @if($log->user)
                            <div class="font-medium text-gray-900 dark:text-gray-100">{{ $log->user->name }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $log->user->email }}</div>
                        @else
                            <span class="text-gray-400 dark:text-gray-500 italic">System / Guest</span>
                        @endif
                    </x-table.td>
                    <x-table.td>
                        @php
                            $badgeColor = match($log->event) {
                                'created' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400',
                                'updated' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400',
                                'deleted', 'force_deleted' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                'restored' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400',
                                'login' => 'bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-400',
                                'logout' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400',
                                default => 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300'
                            };
                        @endphp
                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeColor }}">
                            {{ ucfirst($log->event) }}
                        </span>
                    </x-table.td>
                    <x-table.td>
                        {{ $log->module }}
                    </x-table.td>
                    <x-table.td class="max-w-xs truncate" title="{{ $log->description }}">
                        {{ $log->description }}
                    </x-table.td>
                    <x-table.td class="text-xs">
                        {{ $log->created_at->format('Y-m-d H:i:s') }}
                        <div class="text-gray-400 dark:text-gray-500">{{ $log->created_at->diffForHumans() }}</div>
                    </x-table.td>
                    <x-table.td class="text-right">
                        <a href="{{ route('admin.activity-logs.show', $log) }}" 
                           class="inline-flex items-center justify-center p-2 text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors duration-150" 
                           title="View Activity Details">
                            <i class="fa fa-eye text-sm leading-none"></i>
                        </a>
                    </x-table.td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-10 text-center text-gray-400 dark:text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fa fa-receipt text-3xl mb-3 text-gray-300 dark:text-gray-600"></i>
                            <span class="text-sm">No activity logs found.</span>
                        </div>
                    </td>
                </tr>
            @endforelse
        </x-table>

        <!-- Pagination Links -->
        @if($activityLogs->hasPages())
            <div class="p-6 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                {{ $activityLogs->links() }}
            </div>
        @endif
    </x-card>
</x-admin-layout>
