<x-admin-layout>
    @slot('title')
        App Support & Feedback
    @endslot
    @slot('header')
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                    App Support & Feedback
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Manage issue reports, bug tickets, and service feedback submitted by mobile app users.
                </p>
            </div>
        </div>
    @endslot

    <div class="max-w-7xl mx-auto mt-6 space-y-6">

        @if(session('success'))
            <div class="p-4 text-green-700 bg-green-100 border border-green-200 rounded-lg dark:bg-green-900/30 dark:border-green-800 dark:text-green-300">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 text-red-700 bg-red-100 border border-red-200 rounded-lg dark:bg-red-900/30 dark:border-red-800 dark:text-red-300">
                {{ session('error') }}
            </div>
        @endif

        <!-- Filter Tabs / Stats Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <a href="{{ route('admin.app-supports.index') }}" 
               class="p-4 rounded-xl border transition-all text-center {{ empty($filters['status']) ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:border-indigo-400' }}">
                <div class="text-xs font-semibold uppercase tracking-wider opacity-80">All Reports</div>
                <div class="text-2xl font-bold mt-1">{{ $counts['all'] ?? 0 }}</div>
            </a>
            <a href="{{ route('admin.app-supports.index', ['status' => 'pending']) }}" 
               class="p-4 rounded-xl border transition-all text-center {{ ($filters['status'] ?? '') === 'pending' ? 'bg-amber-500 text-white border-amber-500 shadow-md' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:border-amber-400' }}">
                <div class="text-xs font-semibold uppercase tracking-wider opacity-80">Pending</div>
                <div class="text-2xl font-bold mt-1">{{ $counts['pending'] ?? 0 }}</div>
            </a>
            <a href="{{ route('admin.app-supports.index', ['status' => 'in_progress']) }}" 
               class="p-4 rounded-xl border transition-all text-center {{ ($filters['status'] ?? '') === 'in_progress' ? 'bg-blue-600 text-white border-blue-600 shadow-md' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:border-blue-400' }}">
                <div class="text-xs font-semibold uppercase tracking-wider opacity-80">In Progress</div>
                <div class="text-2xl font-bold mt-1">{{ $counts['in_progress'] ?? 0 }}</div>
            </a>
            <a href="{{ route('admin.app-supports.index', ['status' => 'replied']) }}" 
               class="p-4 rounded-xl border transition-all text-center {{ ($filters['status'] ?? '') === 'replied' ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:border-indigo-400' }}">
                <div class="text-xs font-semibold uppercase tracking-wider opacity-80">Replied</div>
                <div class="text-2xl font-bold mt-1">{{ $counts['replied'] ?? 0 }}</div>
            </a>
            <a href="{{ route('admin.app-supports.index', ['status' => 'resolved']) }}" 
               class="p-4 rounded-xl border transition-all text-center {{ ($filters['status'] ?? '') === 'resolved' ? 'bg-emerald-600 text-white border-emerald-600 shadow-md' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:border-emerald-400' }}">
                <div class="text-xs font-semibold uppercase tracking-wider opacity-80">Resolved</div>
                <div class="text-2xl font-bold mt-1">{{ $counts['resolved'] ?? 0 }}</div>
            </a>
            <a href="{{ route('admin.app-supports.index', ['status' => 'closed']) }}" 
               class="p-4 rounded-xl border transition-all text-center {{ ($filters['status'] ?? '') === 'closed' ? 'bg-gray-700 text-white border-gray-700 shadow-md' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:border-gray-400' }}">
                <div class="text-xs font-semibold uppercase tracking-wider opacity-80">Closed</div>
                <div class="text-2xl font-bold mt-1">{{ $counts['closed'] ?? 0 }}</div>
            </a>
        </div>

        <!-- Search & Filter Bar -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
            <form method="GET" action="{{ route('admin.app-supports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 items-end">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Search Keyword / Ticket</label>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" 
                           placeholder="Ticket #, subject, or user name..." 
                           class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Category</label>
                    <select name="category" class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->value }}" {{ ($filters['category'] ?? '') === $category->value ? 'selected' : '' }}>
                                {{ $category->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Status</label>
                    <select name="status" class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->value }}" {{ ($filters['status'] ?? '') === $status->value ? 'selected' : '' }}>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg text-sm transition">
                        Filter
                    </button>
                    <a href="{{ route('admin.app-supports.index') }}" class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium py-2 px-4 rounded-lg text-sm transition text-center">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Reports Data Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/60">
                        <tr>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ticket #</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Category & Subject</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Device Info</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse($reports as $report)
                            <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/30 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-mono text-xs font-bold text-indigo-600 dark:text-indigo-400">
                                        {{ $report->ticket_no }}
                                    </span>
                                    @if($report->media->count() > 0)
                                        <span class="ml-1 inline-flex items-center text-xs text-gray-500" title="{{ $report->media->count() }} attachment(s)">
                                            📎 {{ $report->media->count() }}
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $report->user->name ?? 'Deleted User' }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $report->user->email ?? 'N/A' }}
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="inline-block px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 mb-1">
                                        {{ $report->category?->label() ?? ucfirst($report->category) }}
                                    </div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-xs">
                                        {{ $report->subject }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                                    @if($report->device_os || $report->app_version)
                                        <div>{{ $report->device_os ?? 'OS N/A' }} ({{ $report->app_version ?? 'v?' }})</div>
                                        <div class="text-gray-400">{{ $report->device_model ?? '' }}</div>
                                    @else
                                        <span class="text-gray-400">N/A</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusClass = match($report->status?->value ?? $report->status) {
                                            'pending'     => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
                                            'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
                                            'replied'     => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300',
                                            'resolved'    => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300',
                                            'closed'      => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                            default       => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-4 font-semibold rounded-full {{ $statusClass }}">
                                        {{ $report->status?->label() ?? ucfirst($report->status) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                                    <div>{{ $report->created_at->format('M d, Y') }}</div>
                                    <div class="text-gray-400">{{ $report->created_at->format('h:i A') }}</div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.app-supports.show', $report->id) }}" 
                                       class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 font-semibold text-xs py-1.5 px-3 rounded-lg bg-indigo-50 dark:bg-indigo-950/40 hover:bg-indigo-100 transition">
                                        Review & Reply &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    <div class="max-w-xs mx-auto">
                                        <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                        </svg>
                                        <p class="mt-2 text-sm font-medium">No support reports found.</p>
                                        <p class="text-xs text-gray-400 mt-1">When users report issues in the app, they will appear here.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($reports->hasPages())
                <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $reports->withQueryString()->links() }}
                </div>
            @endif
        </div>

    </div>
</x-admin-layout>
