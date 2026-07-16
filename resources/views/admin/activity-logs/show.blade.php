<x-admin-layout>
    <x-slot name="title">
        Activity Log Information
    </x-slot>

    <x-slot name="header">
        Activity Log Information
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

        <!-- General Info Card -->
        <x-card title="General Information" class="lg:col-span-2">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <span
                        class="block text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Activity
                        ID</span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">#{{ $activityLog->id }}</span>
                </div>
                <div>
                    <span
                        class="block text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Event</span>
                    @php
                    $badgeColor = match($activityLog->event) {
                    'created' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400',
                    'updated' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400',
                    'deleted', 'force_deleted' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                    'restored' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400',
                    'login' => 'bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-400',
                    'logout' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400',
                    default => 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300'
                    };
                    @endphp
                    <span
                        class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeColor }} mt-1">
                        {{ ucfirst($activityLog->event) }}
                    </span>
                </div>
                <div>
                    <span
                        class="block text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Module</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $activityLog->module }}</span>
                </div>
                <div>
                    <span
                        class="block text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Created
                        At</span>
                    <span class="text-sm text-gray-900 dark:text-gray-100">
                        {{ $activityLog->created_at->format('Y-m-d H:i:s') }}
                        <span class="text-xs text-gray-400 dark:text-gray-500">({{
                            $activityLog->created_at->diffForHumans() }})</span>
                    </span>
                </div>
                <div class="sm:col-span-2">
                    <span
                        class="block text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Description</span>
                    <span
                        class="text-sm text-gray-900 dark:text-gray-100 font-medium block mt-1 bg-gray-50 dark:bg-gray-900/50 p-3 rounded-lg border border-gray-100 dark:border-gray-800">
                        {{ $activityLog->description }}
                    </span>
                </div>
            </div>
        </x-card>

        <!-- User Information Card -->
        <x-card title="User (Actor)">
            <div class="flex flex-col h-full justify-between">
                @if($activityLog->user)
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-700 dark:text-indigo-300 font-bold">
                            {{ strtoupper(substr($activityLog->user->name, 0, 2)) }}
                        </div>
                        <div>
                            <h5 class="font-bold text-gray-900 dark:text-gray-100">{{ $activityLog->user->name }}</h5>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $activityLog->user->email }}</p>
                        </div>
                    </div>
                    <hr class="border-gray-100 dark:border-gray-700" />
                    <div>
                        <span
                            class="block text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">User
                            ID</span>
                        <span class="text-sm text-gray-900 dark:text-gray-100 font-mono">#{{ $activityLog->user_id
                            }}</span>
                    </div>
                    <div>
                        <a href="{{ route('admin.users.activity-logs', $activityLog->user_id) }}"
                            class="inline-flex items-center text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
                            View User Activity History <i class="fa fa-arrow-right ml-1 text-[10px]"></i>
                        </a>
                    </div>
                </div>
                @else
                <div class="flex flex-col items-center justify-center py-6 text-gray-400 dark:text-gray-500">
                    <i class="fa fa-robot text-4xl mb-2 text-gray-300 dark:text-gray-700"></i>
                    <span class="font-semibold">System / Guest</span>
                    <span class="text-xs">No authenticated user associated.</span>
                </div>
                @endif
            </div>
        </x-card>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

        <!-- Subject Info Card -->
        <x-card title="Subject (Target Object)">
            <div class="space-y-4">
                <div>
                    <span
                        class="block text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Subject
                        Type</span>
                    <span class="text-sm font-mono text-gray-900 dark:text-gray-100">{{ $activityLog->subject_type ??
                        'N/A' }}</span>
                </div>
                <div>
                    <span
                        class="block text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Subject
                        ID</span>
                    <span class="text-sm font-mono text-gray-900 dark:text-gray-100">{{ $activityLog->subject_id ??
                        'N/A' }}</span>
                </div>
                <div>
                    <span
                        class="block text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Subject
                        Status</span>
                    <div class="mt-1">
                        @if($activityLog->subject)
                        <span
                            class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-600 dark:text-emerald-400">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            Record Active
                        </span>
                        @php
                        $subjectRoute = null;
                        try {
                        if ($activityLog->subject_type === 'App\Models\User') {
                        $subjectRoute = route('admin.users.edit', $activityLog->subject_id);
                        }
                        } catch (\Exception $e) {}
                        @endphp
                        @if($subjectRoute)
                        <div class="mt-2">
                            <a href="{{ $subjectRoute }}"
                                class="inline-flex items-center text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                                View Related Record <i class="fa fa-external-link-alt ml-1 text-[10px]"></i>
                            </a>
                        </div>
                        @endif
                        @else
                        <span
                            class="inline-flex items-center gap-1.5 text-sm font-semibold text-red-500 dark:text-red-400">
                            <span class="h-2 w-2 rounded-full bg-red-500"></span>
                            Record No Longer Exists
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Request Details -->
        <x-card title="Request Context" class="lg:col-span-2">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <span
                        class="block text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">IP
                        Address</span>
                    <span class="text-sm font-mono text-gray-900 dark:text-gray-100">{{ $activityLog->ip_address ??
                        'N/A' }}</span>
                </div>
                <div>
                    <span
                        class="block text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">HTTP
                        Method</span>
                    <span
                        class="px-2 py-0.5 rounded text-xs font-bold uppercase tracking-wide bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-400 inline-block mt-0.5">
                        {{ $activityLog->method ?? 'N/A' }}
                    </span>
                </div>
                <div class="sm:col-span-2">
                    <span
                        class="block text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Request
                        URL</span>
                    <span class="text-sm font-mono text-gray-900 dark:text-gray-100 break-all select-all">{{
                        $activityLog->url ?? 'N/A' }}</span>
                </div>
                <div class="sm:col-span-2">
                    <span
                        class="block text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">User
                        Agent</span>
                    <span
                        class="text-xs text-gray-600 dark:text-gray-400 block mt-1 bg-gray-50 dark:bg-gray-900/50 p-2.5 rounded-lg border border-gray-100 dark:border-gray-800 break-all select-all font-mono leading-relaxed">
                        {{ $activityLog->user_agent ?? 'N/A' }}
                    </span>
                </div>
            </div>
        </x-card>

    </div>

    <!-- Trace Context -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <x-card title="Tracing & Batch Audit" class="lg:col-span-3">
            <div>
                <span
                    class="block text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Batch
                    UUID</span>
                <span
                    class="text-sm font-mono font-bold text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-950 px-3 py-1.5 rounded-md border border-gray-100 dark:border-gray-900 inline-block mt-1 break-all select-all">
                    {{ $activityLog->batch_uuid ?? 'N/A' }}
                </span>
            </div>
        </x-card>
    </div>

    <!-- Data Delta Changes -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        <!-- Old Values -->
        <x-card title="Old Values (Before Change)">
            @if(!empty($activityLog->old_values))
            <pre
                class="text-xs font-mono bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto select-all max-h-96"><code>{{ json_encode($activityLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
            @else
            <div class="text-center py-10 text-gray-400 dark:text-gray-500 italic text-sm">
                No old values recorded.
            </div>
            @endif
        </x-card>

        <!-- New Values -->
        <x-card title="New Values (After Change)">
            @if(!empty($activityLog->new_values))
            <pre
                class="text-xs font-mono bg-gray-900 text-blue-400 p-4 rounded-lg overflow-x-auto select-all max-h-96"><code>{{ json_encode($activityLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
            @else
            <div class="text-center py-10 text-gray-400 dark:text-gray-500 italic text-sm">
                No new values recorded.
            </div>
            @endif
        </x-card>

    </div>

    <!-- Properties -->
    <div class="grid grid-cols-1 gap-6">
        <x-card title="Extended Properties / Meta">
            @if(!empty($activityLog->properties))
            <pre
                class="text-xs font-mono bg-gray-900 text-yellow-400 p-4 rounded-lg overflow-x-auto select-all max-h-96"><code>{{ json_encode($activityLog->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
            @else
            <div class="text-center py-10 text-gray-400 dark:text-gray-500 italic text-sm">
                No properties/meta recorded.
            </div>
            @endif
        </x-card>
    </div>

</x-admin-layout>