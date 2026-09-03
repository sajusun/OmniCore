<x-admin-layout>
    @slot('title')
        Support #{{ $report->ticket_no }} — {{ $report->subject }}
    @endslot

    @slot('header')
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.app-supports.index') }}" 
                   class="p-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-600 hover:text-gray-900 dark:text-gray-300 transition">
                    &larr; Back
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-xs font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/60 px-2 py-0.5 rounded">
                            {{ $report->ticket_no }}
                        </span>
                        <span class="text-xs px-2.5 py-0.5 rounded-full font-semibold {{ $report->status?->badgeClass() ?? 'bg-gray-100' }}">
                            {{ $report->status?->label() ?? ucfirst($report->status) }}
                        </span>
                    </div>
                    <h2 class="font-bold text-xl text-gray-900 dark:text-white mt-1">
                        {{ $report->subject }}
                    </h2>
                </div>
            </div>

            <!-- Quick Status Change Action -->
            <form action="{{ route('admin.app-supports.status', $report->id) }}" method="POST" class="flex items-center gap-2">
                @csrf
                @method('PATCH')
                <select name="status" class="text-xs font-medium rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500">
                    @foreach($statuses as $status)
                        <option value="{{ $status->value }}" {{ ($report->status?->value ?? $report->status) === $status->value ? 'selected' : '' }}>
                            Mark as: {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white dark:bg-gray-700 dark:hover:bg-gray-600 text-xs font-semibold py-2 px-3 rounded-lg transition">
                    Update Status
                </button>
            </form>
        </div>
    @endslot

    <div class="max-w-7xl mx-auto mt-6">

        @if(session('success'))
            <div class="mb-6 p-4 text-green-700 bg-green-100 border border-green-200 rounded-lg dark:bg-green-900/30 dark:border-green-800 dark:text-green-300 flex items-center justify-between">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 text-red-700 bg-red-100 border border-red-200 rounded-lg dark:bg-red-900/30 dark:border-red-800 dark:text-red-300">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left & Middle: Issue Details & Replies Thread (2 cols) -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Original User Issue Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 flex items-center justify-center font-bold text-sm">
                                {{ strtoupper(substr($report->user->name ?? 'U', 0, 2)) }}
                            </div>
                            <div>
                                <div class="font-bold text-gray-900 dark:text-white text-sm">
                                    {{ $report->user->name ?? 'User' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    Reported {{ $report->created_at->format('M d, Y \a\t h:i A') }} ({{ $report->created_at->diffForHumans() }})
                                </div>
                            </div>
                        </div>

                        <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                            {{ $report->category?->label() ?? ucfirst($report->category) }}
                        </span>
                    </div>

                    <!-- Message Body -->
                    <div class="text-gray-800 dark:text-gray-200 text-sm leading-relaxed whitespace-pre-wrap">
                        {{ $report->message }}
                    </div>

                    <!-- Attached Screenshots / Media -->
                    @if($report->media && $report->media->count() > 0)
                        <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <h4 class="text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-3">
                                Attached Screenshots / Files ({{ $report->media->count() }})
                            </h4>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                @foreach($report->media as $media)
                                    @php
                                        $isImage = in_array(strtolower($media->extension ?? ''), ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                    @endphp
                                    <a href="{{ $media->full_url }}" target="_blank" 
                                       class="group block p-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 hover:border-indigo-400 transition overflow-hidden">
                                        @if($isImage)
                                            <div class="aspect-video w-full rounded-lg overflow-hidden bg-gray-200 dark:bg-gray-800 mb-2">
                                                <img src="{{ $media->full_url }}" alt="{{ $media->original_name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-200">
                                            </div>
                                        @else
                                            <div class="aspect-video w-full rounded-lg flex items-center justify-center bg-gray-200 dark:bg-gray-800 text-gray-500 mb-2 text-2xl">
                                                📄
                                            </div>
                                        @endif
                                        <div class="text-xs font-medium text-gray-700 dark:text-gray-300 truncate" title="{{ $media->original_name }}">
                                            {{ $media->original_name }}
                                        </div>
                                        <div class="text-[10px] text-gray-400">
                                            {{ number_format(($media->size ?? 0) / 1024, 1) }} KB &bull; View &rarr;
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Conversation History / Replies Timeline -->
                @if($report->replies->count() > 0)
                    <div class="space-y-4">
                        <h3 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider px-2">
                            Responses Timeline ({{ $report->replies->count() }})
                        </h3>

                        @foreach($report->replies as $reply)
                            @if($reply->sender_type === 'system')
                                <!-- System Message -->
                                <div class="bg-gray-50 dark:bg-gray-800/60 border border-dashed border-gray-300 dark:border-gray-700 rounded-xl p-4 text-xs text-gray-600 dark:text-gray-400 flex items-start gap-3">
                                    <span class="text-base">🤖</span>
                                    <div>
                                        <div class="font-semibold text-gray-700 dark:text-gray-300">
                                            Automated System Notification &bull; {{ $reply->created_at->format('M d, h:i A') }}
                                        </div>
                                        <p class="mt-1">{{ $reply->message }}</p>
                                    </div>
                                </div>
                            @elseif($reply->sender_type === 'admin')
                                <!-- Admin Reply -->
                                <div class="bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800/60 rounded-2xl p-5 shadow-sm">
                                    <div class="flex items-center justify-between pb-3 mb-3 border-b border-indigo-100 dark:border-indigo-900/60">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-indigo-600 text-white uppercase tracking-wider">
                                                Admin Response
                                            </span>
                                            <span class="font-bold text-sm text-gray-900 dark:text-white">
                                                {{ $reply->author->name ?? 'Administrator' }}
                                            </span>
                                        </div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $reply->created_at->format('M d, Y \a\t h:i A') }}
                                        </span>
                                    </div>

                                    <div class="text-gray-800 dark:text-gray-200 text-sm leading-relaxed whitespace-pre-wrap">
                                        {{ $reply->message }}
                                    </div>

                                    @if($reply->media && $reply->media->count() > 0)
                                        <div class="mt-3 pt-3 border-t border-indigo-100 dark:border-indigo-900/60 flex flex-wrap gap-2">
                                            @foreach($reply->media as $rMedia)
                                                <a href="{{ $rMedia->full_url }}" target="_blank" class="text-xs text-indigo-600 dark:text-indigo-400 underline font-medium">
                                                    📎 {{ $rMedia->original_name }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @else
                                <!-- User Reply (Future 2-Way Ready) -->
                                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-5 shadow-sm">
                                    <div class="flex items-center justify-between pb-3 mb-3 border-b border-gray-100 dark:border-gray-700">
                                        <span class="font-bold text-sm text-gray-900 dark:text-white">
                                            {{ $reply->author->name ?? 'User' }}
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            {{ $reply->created_at->format('M d, Y \a\t h:i A') }}
                                        </span>
                                    </div>
                                    <div class="text-gray-800 dark:text-gray-200 text-sm leading-relaxed whitespace-pre-wrap">
                                        {{ $reply->message }}
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif

                <!-- Reply Composer Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span>✍️</span> Send Response to User
                        </h3>
                        <span class="text-xs text-indigo-600 dark:text-indigo-400 font-medium">
                            🔔 Sends Push Notification + ✉️ Email
                        </span>
                    </div>

                    <form action="{{ route('admin.app-supports.reply', $report->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                Message to User <span class="text-red-500">*</span>
                            </label>
                            <textarea name="message" rows="5" required 
                                      placeholder="Write your resolution or update to the user. This will be sent directly to their email and phone notification..."
                                      class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                            @error('message')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-end">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                    Update Status to
                                </label>
                                <select name="status" class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500">
                                    <option value="replied" selected>Replied (Keep Open)</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="resolved">Resolved</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                    Attach Files / Images (Optional)
                                </label>
                                <input type="file" name="attachments[]" multiple 
                                       class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            </div>
                        </div>

                        <div class="pt-3 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                            <button type="submit" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-6 rounded-xl text-sm transition shadow-sm">
                                <span>Send Reply & Notify User</span>
                                <span>&rarr;</span>
                            </button>
                        </div>
                    </form>
                </div>

            </div>

            <!-- Right Column: User Profile & Device Information (1 col) -->
            <div class="space-y-6">

                <!-- User Information Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                    <h3 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">
                        User Profile
                    </h3>

                    <div class="flex items-center gap-3 pb-4 mb-4 border-b border-gray-100 dark:border-gray-700">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-600 text-white flex items-center justify-center font-bold text-lg">
                            {{ strtoupper(substr($report->user->name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <div class="font-bold text-gray-900 dark:text-white">
                                {{ $report->user->name ?? 'User N/A' }}
                            </div>
                            <div class="text-xs text-gray-500">
                                ID: #{{ $report->user->id ?? 'N/A' }}
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2.5 text-xs">
                        <div class="flex justify-between py-1 border-b border-gray-50 dark:border-gray-700/50">
                            <span class="text-gray-500">Email:</span>
                            <span class="font-medium text-gray-800 dark:text-gray-200">{{ $report->user->email ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-gray-50 dark:border-gray-700/50">
                            <span class="text-gray-500">Phone:</span>
                            <span class="font-medium text-gray-800 dark:text-gray-200">{{ $report->user->phone ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span class="text-gray-500">Joined:</span>
                            <span class="font-medium text-gray-800 dark:text-gray-200">{{ $report->user->created_at ? $report->user->created_at->format('M d, Y') : 'N/A' }}</span>
                        </div>
                    </div>

                    @if(!empty($report->user))
                        <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700 text-center">
                            <a href="{{ route('admin.users.show', $report->user->id) }}" target="_blank" class="text-xs text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">
                                View Full User Profile &rarr;
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Device & System Diagnostics Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                    <h3 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-1.5">
                        <span>📱</span> Device Diagnostics
                    </h3>

                    <div class="space-y-3 text-xs">
                        <div class="p-2.5 rounded-lg bg-gray-50 dark:bg-gray-900/50 flex justify-between items-center">
                            <span class="text-gray-500">Operating System:</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $report->device_os ?: 'Not provided' }}</span>
                        </div>
                        <div class="p-2.5 rounded-lg bg-gray-50 dark:bg-gray-900/50 flex justify-between items-center">
                            <span class="text-gray-500">Device Model:</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $report->device_model ?: 'Not provided' }}</span>
                        </div>
                        <div class="p-2.5 rounded-lg bg-gray-50 dark:bg-gray-900/50 flex justify-between items-center">
                            <span class="text-gray-500">App Version:</span>
                            <span class="font-semibold text-indigo-600 dark:text-indigo-400 font-mono">{{ $report->app_version ?: 'N/A' }}</span>
                        </div>
                        <div class="p-2.5 rounded-lg bg-gray-50 dark:bg-gray-900/50 flex justify-between items-center">
                            <span class="text-gray-500">Submitted At:</span>
                            <span class="font-medium text-gray-800 dark:text-gray-200">{{ $report->created_at->format('M d, Y h:i A') }}</span>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
</x-admin-layout>
