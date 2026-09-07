<x-admin-layout>
    <x-slot name="title">Wallet Ledger Transactions</x-slot>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Double-Entry Wallet Ledger</h2>
            <a href="{{ route('admin.wallets.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm hover:bg-gray-200">
                Back to Wallets
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filter -->
            <div class="bg-white p-4 shadow-sm border border-gray-100 mb-6">
                <form method="GET" action="{{ route('admin.wallets.transactions') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Trx ID, User name/email..." class="px-3 py-2 border text-sm">
                    <select name="type" class="px-3 py-2 border text-sm">
                        <option value="">All Transaction Types</option>
                        <option value="deposit" {{ request('type') === 'deposit' ? 'selected' : '' }}>Deposit</option>
                        <option value="withdrawal" {{ request('type') === 'withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                        <option value="transfer_sent" {{ request('type') === 'transfer_sent' ? 'selected' : '' }}>Transfer Sent</option>
                        <option value="transfer_received" {{ request('type') === 'transfer_received' ? 'selected' : '' }}>Transfer Received</option>
                        <option value="payment" {{ request('type') === 'payment' ? 'selected' : '' }}>Payment</option>
                        <option value="refund" {{ request('type') === 'refund' ? 'selected' : '' }}>Refund</option>
                        <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>Admin Adjustment</option>
                    </select>
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700">Filter</button>
                        <a href="{{ route('admin.wallets.transactions') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm hover:bg-gray-200">Reset</a>
                    </div>
                </form>
            </div>

            <!-- Ledger Table -->
            <div class="bg-white shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead class="bg-gray-50 border-b text-gray-600 uppercase text-xs">
                            <tr>
                                <th class="p-3">Trx ID</th>
                                <th class="p-3">User</th>
                                <th class="p-3">Type</th>
                                <th class="p-3">Amount</th>
                                <th class="p-3">Balance (Before &rarr; After)</th>
                                <th class="p-3">Description</th>
                                <th class="p-3">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 font-mono text-xs">
                            @forelse($transactions as $trx)
                            <tr class="hover:bg-gray-50">
                                <td class="p-3 font-semibold text-gray-800">{{ $trx->trx_id }}</td>
                                <td class="p-3 font-sans text-sm">
                                    <div class="font-medium text-gray-900">{{ $trx->user?->name ?? 'User #' . $trx->user_id }}</div>
                                    <div class="text-xs text-gray-500">{{ $trx->user?->email }}</div>
                                </td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 uppercase text-[11px] font-semibold font-sans
 {{ in_array($trx->type?->value ?? $trx->type, ['deposit', 'refund', 'transfer_received']) ? 'bg-green-100 text-green-800' : 'bg-rose-100 text-rose-800' }}">
                                        {{ str_replace('_', ' ', $trx->type?->value ?? $trx->type) }}
                                    </span>
                                </td>
                                <td class="p-3 font-bold text-sm font-sans {{ in_array($trx->type?->value ?? $trx->type, ['deposit', 'refund', 'transfer_received']) ? 'text-green-600' : 'text-rose-600' }}">
                                    {{ in_array($trx->type?->value ?? $trx->type, ['deposit', 'refund', 'transfer_received']) ? '+' : '-' }}${{ number_format($trx->amount, 2) }}
                                </td>
                                <td class="p-3 text-gray-600">
                                    ${{ number_format($trx->balance_before, 2) }} &rarr; <span class="font-bold text-gray-900">${{ number_format($trx->balance_after, 2) }}</span>
                                </td>
                                <td class="p-3 font-sans text-xs text-gray-600 max-w-xs truncate">
                                    {{ $trx->description }}
                                </td>
                                <td class="p-3 font-sans text-xs text-gray-500">
                                    {{ $trx->created_at->format('M d, Y H:i:s') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-gray-400 font-sans text-sm">No ledger transactions found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t">
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
