<x-admin-layout>
    <x-slot name="title">User Wallets</x-slot>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">User Wallets & Balances</h2>
            <a href="{{ route('wallets.transactions') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-md text-sm font-semibold hover:bg-emerald-700">
                View Ledger Transactions
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm border border-green-200 mb-6">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="p-4 bg-red-50 text-red-700 rounded-lg text-sm border border-red-200 mb-6">
                {{ session('error') }}
            </div>
            @endif

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Total User Wallets</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($stats['total_wallets']) }}</p>
                </div>
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Total Circulation</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">${{ number_format($stats['total_circulation'], 2) }}</p>
                </div>
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Frozen Wallets</p>
                    <p class="text-2xl font-bold text-rose-600 mt-1">{{ number_format($stats['frozen_wallets']) }}</p>
                </div>
            </div>

            <!-- Search -->
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 mb-6">
                <form method="GET" action="{{ route('wallets.index') }}" class="flex gap-4">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search user name or email..." class="flex-1 px-3 py-2 border rounded-md text-sm">
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md text-sm font-semibold hover:bg-emerald-700">Search</button>
                    <a href="{{ route('wallets.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm hover:bg-gray-200">Reset</a>
                </form>
            </div>

            <!-- Wallets Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead class="bg-gray-50 border-b text-gray-600 uppercase text-xs">
                            <tr>
                                <th class="p-3">User</th>
                                <th class="p-3">Balance</th>
                                <th class="p-3">Status</th>
                                <th class="p-3">Last Updated</th>
                                <th class="p-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($wallets as $wallet)
                            <tr class="hover:bg-gray-50">
                                <td class="p-3">
                                    <div class="font-medium text-gray-900">{{ $wallet->user?->name ?? 'User #' . $wallet->user_id }}</div>
                                    <div class="text-xs text-gray-500">{{ $wallet->user?->email }}</div>
                                </td>
                                <td class="p-3 font-bold text-gray-900 text-base">
                                    ${{ number_format($wallet->balance, 2) }}
                                    <span class="text-xs text-gray-400 font-normal">{{ $wallet->currency }}</span>
                                </td>
                                <td class="p-3">
                                    @if($wallet->is_frozen)
                                        <span class="px-2 py-1 bg-rose-100 text-rose-700 text-xs font-semibold rounded-full">Frozen</span>
                                    @elseif(!$wallet->is_active)
                                        <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-full">Inactive</span>
                                    @else
                                        <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-full">Active</span>
                                    @endif
                                </td>
                                <td class="p-3 text-xs text-gray-500">
                                    {{ $wallet->updated_at->diffForHumans() }}
                                </td>
                                <td class="p-3 text-right space-x-2">
                                    <!-- Toggle Freeze -->
                                    <form method="POST" action="{{ route('wallets.toggle-freeze', $wallet) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 text-xs font-medium rounded border {{ $wallet->is_frozen ? 'border-emerald-300 text-emerald-700 hover:bg-emerald-50' : 'border-rose-300 text-rose-700 hover:bg-rose-50' }}">
                                            {{ $wallet->is_frozen ? 'Unfreeze' : 'Freeze' }}
                                        </button>
                                    </form>

                                    <!-- Adjust Modal / Form trigger -->
                                    <button onclick="document.getElementById('adjust-modal-{{ $wallet->id }}').classList.remove('hidden')" class="px-2.5 py-1 text-xs font-medium rounded bg-gray-100 hover:bg-gray-200 text-gray-700">
                                        Adjust Balance
                                    </button>

                                    <!-- Modal -->
                                    <div id="adjust-modal-{{ $wallet->id }}" class="fixed inset-0 bg-black/40 hidden z-50 flex items-center justify-center text-left">
                                        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4 shadow-xl">
                                            <h3 class="font-bold text-gray-900 text-base mb-2">Adjust Balance: {{ $wallet->user?->name }}</h3>
                                            <p class="text-xs text-gray-500 mb-4">Current balance: ${{ number_format($wallet->balance, 2) }} {{ $wallet->currency }}</p>
                                            
                                            <form method="POST" action="{{ route('wallets.adjust-balance', $wallet) }}" class="space-y-4">
                                                @csrf
                                                <div>
                                                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Adjustment Type</label>
                                                    <select name="type" class="w-full border rounded-md px-3 py-2 text-sm">
                                                        <option value="credit">Credit (Add Money)</option>
                                                        <option value="debit">Debit (Deduct Money)</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Amount</label>
                                                    <input type="number" step="0.01" name="amount" required placeholder="0.00" class="w-full border rounded-md px-3 py-2 text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Reason / Note</label>
                                                    <input type="text" name="reason" required placeholder="e.g. Promotional bonus, Correction" class="w-full border rounded-md px-3 py-2 text-sm">
                                                </div>
                                                <div class="flex justify-end gap-2 pt-2">
                                                    <button type="button" onclick="document.getElementById('adjust-modal-{{ $wallet->id }}').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm">Cancel</button>
                                                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md text-sm font-semibold hover:bg-emerald-700">Submit Adjustment</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-gray-400">No user wallets found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t">
                    {{ $wallets->links() }}
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
