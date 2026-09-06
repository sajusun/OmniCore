<x-admin-layout>
    <x-slot name="title">User Subscriptions</x-slot>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Subscriber Management</h2>
            <button onclick="document.getElementById('grant-modal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white rounded-md text-sm font-semibold hover:bg-emerald-700">
                + Manually Grant Subscription
            </button>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
            <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm border border-green-200">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="p-4 bg-red-50 text-red-700 rounded-lg text-sm border border-red-200">
                {{ session('error') }}
            </div>
            @endif

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Total Subscriptions</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($stats['total_subscriptions']) }}</p>
                </div>
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Active Subscribers</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($stats['active_count']) }}</p>
                </div>
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase">On Trial</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($stats['trial_count']) }}</p>
                </div>
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Canceled / Expired</p>
                    <p class="text-2xl font-bold text-gray-500 mt-1">{{ number_format($stats['canceled_count']) }}</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                <form method="GET" action="{{ route('admin.subscriptions.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search User Name, Email..." class="px-3 py-2 border rounded-md text-sm">
                    <select name="plan_id" class="px-3 py-2 border rounded-md text-sm">
                        <option value="">All Plans</option>
                        @foreach($plans as $p)
                        <option value="{{ $p->id }}" {{ request('plan_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                    <select name="status" class="px-3 py-2 border rounded-md text-sm">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="trialing" {{ request('status') === 'trialing' ? 'selected' : '' }}>Trialing</option>
                        <option value="canceled" {{ request('status') === 'canceled' ? 'selected' : '' }}>Canceled</option>
                        <option value="past_due" {{ request('status') === 'past_due' ? 'selected' : '' }}>Past Due</option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md text-sm font-semibold hover:bg-emerald-700">Filter</button>
                        <a href="{{ route('admin.subscriptions.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm hover:bg-gray-200">Reset</a>
                    </div>
                </form>
            </div>

            <!-- Subscriptions Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead class="bg-gray-50 border-b text-gray-600 uppercase text-xs">
                            <tr>
                                <th class="p-3">User</th>
                                <th class="p-3">Plan</th>
                                <th class="p-3">Status</th>
                                <th class="p-3">Started</th>
                                <th class="p-3">Renews / Ends</th>
                                <th class="p-3">Auto Renew</th>
                                <th class="p-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($subscriptions as $sub)
                            <tr class="hover:bg-gray-50">
                                <td class="p-3">
                                    <div class="font-medium text-gray-900">{{ $sub->user?->name ?? 'User #' . $sub->user_id }}</div>
                                    <div class="text-xs text-gray-500">{{ $sub->user?->email }}</div>
                                </td>
                                <td class="p-3 font-semibold text-gray-800">
                                    {{ $sub->plan?->name ?? 'Plan #' . $sub->plan_id }}
                                    <span class="text-xs text-gray-500 font-normal">(${{ number_format($sub->plan?->price ?? 0, 2) }})</span>
                                </td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full
                                        {{ $sub->status?->value === 'active' ? 'bg-green-100 text-green-700' : '' }}
                                        {{ $sub->status?->value === 'trialing' ? 'bg-blue-100 text-blue-700' : '' }}
                                        {{ $sub->status?->value === 'canceled' ? 'bg-gray-100 text-gray-700' : '' }}
                                        {{ $sub->status?->value === 'past_due' ? 'bg-rose-100 text-rose-700' : '' }}">
                                        {{ ucfirst($sub->status?->value ?? $sub->status) }}
                                    </span>
                                </td>
                                <td class="p-3 text-xs text-gray-600">
                                    {{ $sub->starts_at ? $sub->starts_at->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="p-3 text-xs text-gray-600">
                                    {{ $sub->ends_at ? $sub->ends_at->format('M d, Y') : 'Lifetime' }}
                                </td>
                                <td class="p-3">
                                    <span class="text-xs {{ $sub->auto_renew ? 'text-green-600 font-semibold' : 'text-gray-400' }}">
                                        {{ $sub->auto_renew ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                                <td class="p-3 text-right space-x-2">
                                    <a href="{{ route('admin.subscriptions.show', $sub) }}" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs rounded font-medium">
                                        Inspect
                                    </a>

                                    @if($sub->isActive())
                                    <form method="POST" action="{{ route('admin.subscriptions.cancel', $sub) }}" class="inline" onsubmit="return confirm('Cancel this subscription?')">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 text-xs font-medium rounded border border-rose-200 text-rose-600 hover:bg-rose-50">
                                            Cancel
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-gray-400">No subscribers found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t">
                    {{ $subscriptions->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Grant Modal -->
    <div id="grant-modal" class="fixed inset-0 bg-black/40 hidden z-50 flex items-center justify-center text-left">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4 shadow-xl">
            <h3 class="font-bold text-gray-900 text-base mb-2">Manually Grant Subscription</h3>
            <p class="text-xs text-gray-500 mb-4">Grant free VIP/plan access to a user without billing.</p>
            
            <form method="POST" action="{{ route('admin.subscriptions.grant') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">User ID</label>
                    <input type="number" name="user_id" required placeholder="User ID" class="w-full border rounded-md px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Plan</label>
                    <select name="plan_id" class="w-full border rounded-md px-3 py-2 text-sm">
                        @foreach($plans as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} (${{ number_format($p->price, 2) }}/{{ $p->billing_interval }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('grant-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md text-sm font-semibold hover:bg-emerald-700">Grant Subscription</button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
