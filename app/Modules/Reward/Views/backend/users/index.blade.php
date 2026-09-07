<x-admin-layout>
    <x-slot name="title">User Reward Balances</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">User Points & Loyalty Tiers</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
            <div class="p-4 bg-green-50 text-green-700 text-sm border border-green-200">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="p-4 bg-red-50 text-red-700 text-sm border border-red-200">
                {{ session('error') }}
            </div>
            @endif

            <!-- Stats Row -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white p-5 shadow-sm border border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Enrolled Members</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($stats['total_users_enrolled']) }}</p>
                </div>
                <div class="bg-white p-5 shadow-sm border border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Points in Circulation</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($stats['total_points_in_circulation']) }} pts</p>
                </div>
                <div class="bg-white p-5 shadow-sm border border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Total Points Distributed</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($stats['total_points_distributed']) }} pts</p>
                </div>
            </div>

            <!-- Search & Filters -->
            <div class="bg-white p-4 shadow-sm border border-gray-100">
                <form method="GET" action="{{ route('admin.rewards.users') }}" class="flex gap-4">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search User Name, Email..." class="flex-1 px-3 py-2 border text-sm">
                    <select name="tier_id" class="px-3 py-2 border text-sm">
                        <option value="">All Tiers</option>
                        @foreach($tiers as $t)
                        <option value="{{ $t->id }}" {{ request('tier_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700">Filter</button>
                    <a href="{{ route('admin.rewards.users') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm hover:bg-gray-200">Reset</a>
                </form>
            </div>

            <!-- Table -->
            <div class="bg-white shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead class="bg-gray-50 border-b text-gray-600 uppercase text-xs">
                            <tr>
                                <th class="p-3">User</th>
                                <th class="p-3">Current Tier</th>
                                <th class="p-3">Current Points</th>
                                <th class="p-3">Lifetime Points</th>
                                <th class="p-3">Streak</th>
                                <th class="p-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($userRewards as $ur)
                            <tr class="hover:bg-gray-50">
                                <td class="p-3">
                                    <div class="font-medium text-gray-900">{{ $ur->user?->name ?? 'User #' . $ur->user_id }}</div>
                                    <div class="text-xs text-gray-500">{{ $ur->user?->email }}</div>
                                </td>
                                <td class="p-3 font-semibold">
                                    <span class="px-2.5 py-0.5 text-xs font-bold text-white" style="background-color: {{ $ur->tier?->color ?? '#6b7280' }}">
                                        {{ $ur->tier?->name ?? 'Unranked' }}
                                    </span>
                                </td>
                                <td class="p-3 font-bold text-emerald-600 text-base">
                                    {{ number_format($ur->points_balance) }} pts
                                </td>
                                <td class="p-3 text-gray-600">
                                    {{ number_format($ur->lifetime_points) }} pts
                                </td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 bg-amber-50 text-amber-700 text-xs font-semibold">
                                        🔥 {{ $ur->streak_days }} days
                                    </span>
                                </td>
                                <td class="p-3 text-right">
                                    <button onclick="document.getElementById('adjust-modal-{{ $ur->id }}').classList.remove('hidden')" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium">
                                        Adjust Points
                                    </button>

                                    <!-- Adjust Modal -->
                                    <div id="adjust-modal-{{ $ur->id }}" class="fixed inset-0 bg-black/40 hidden z-50 flex items-center justify-center text-left">
                                        <div class="bg-white p-6 max-w-md w-full mx-4 shadow-xl">
                                            <h3 class="font-bold text-gray-900 text-base mb-1">Adjust Points: {{ $ur->user?->name }}</h3>
                                            <p class="text-xs text-gray-500 mb-4">Current points: {{ number_format($ur->points_balance) }}</p>
                                            
                                            <form method="POST" action="{{ route('admin.rewards.adjust', $ur->user) }}" class="space-y-4">
                                                @csrf
                                                <div>
                                                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Action</label>
                                                    <select name="type" class="w-full border px-3 py-2 text-sm">
                                                        <option value="credit">Credit Points (Add)</option>
                                                        <option value="debit">Debit Points (Deduct)</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Points Amount</label>
                                                    <input type="number" name="amount" required placeholder="100" class="w-full border px-3 py-2 text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Reason / Note</label>
                                                    <input type="text" name="description" required placeholder="e.g. Compensation, Campaign bonus" class="w-full border px-3 py-2 text-sm">
                                                </div>
                                                <div class="flex justify-end gap-2 pt-2">
                                                    <button type="button" onclick="document.getElementById('adjust-modal-{{ $ur->id }}').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm">Cancel</button>
                                                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700">Submit Adjustment</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-gray-400">No member reward records found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t">
                    {{ $userRewards->links() }}
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
