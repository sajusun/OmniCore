<x-admin-layout>
    <x-slot name="title">Payment Transactions</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Payment Transactions</h2>
    </x-slot>

    @push('styles')
    <style>
        .badge-completed { background-color: #10b981; color: white; }
        .badge-pending { background-color: #f59e0b; color: white; }
        .badge-failed { background-color: #ef4444; color: white; }
        .badge-refunded { background-color: #6b7280; color: white; }
    </style>
    @endpush

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Row -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Total Payments</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($stats['total_payments']) }}</p>
                </div>
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Total Revenue</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">${{ number_format($stats['total_revenue'], 2) }}</p>
                </div>
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Completed</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ number_format($stats['completed_count']) }}</p>
                </div>
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Pending</p>
                    <p class="text-2xl font-bold text-amber-500 mt-1">{{ number_format($stats['pending_count']) }}</p>
                </div>
            </div>

            <!-- Filters & Actions -->
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 mb-6">
                <form method="GET" action="{{ route('payments.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Payment ID, Trx ID, User..." class="w-full px-3 py-2 border rounded-md text-sm">
                    </div>
                    <div>
                        <select name="status" class="w-full px-3 py-2 border rounded-md text-sm">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                        </select>
                    </div>
                    <div>
                        <select name="method" class="w-full px-3 py-2 border rounded-md text-sm">
                            <option value="">All Methods</option>
                            <option value="stripe" {{ request('method') === 'stripe' ? 'selected' : '' }}>Stripe</option>
                            <option value="paypal" {{ request('method') === 'paypal' ? 'selected' : '' }}>PayPal</option>
                            <option value="sslcommerz" {{ request('method') === 'sslcommerz' ? 'selected' : '' }}>SSLCommerz</option>
                            <option value="bkash" {{ request('method') === 'bkash' ? 'selected' : '' }}>bKash</option>
                            <option value="wallet" {{ request('method') === 'wallet' ? 'selected' : '' }}>Wallet</option>
                            <option value="manual_bank" {{ request('method') === 'manual_bank' ? 'selected' : '' }}>Manual Bank</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md text-sm font-semibold hover:bg-emerald-700">Filter</button>
                        <a href="{{ route('payments.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm hover:bg-gray-200">Reset</a>
                    </div>
                </form>
            </div>

            <!-- Payments Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead class="bg-gray-50 border-b text-gray-600 uppercase text-xs">
                            <tr>
                                <th class="p-3">Payment ID</th>
                                <th class="p-3">User</th>
                                <th class="p-3">For</th>
                                <th class="p-3">Amount</th>
                                <th class="p-3">Method</th>
                                <th class="p-3">Status</th>
                                <th class="p-3">Date</th>
                                <th class="p-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($payments as $payment)
                            <tr class="hover:bg-gray-50">
                                <td class="p-3 font-mono font-medium text-gray-800">
                                    <a href="{{ route('payments.show', $payment) }}" class="text-blue-600 hover:underline">
                                        {{ $payment->payment_id }}
                                    </a>
                                </td>
                                <td class="p-3">
                                    <div class="font-medium text-gray-900">{{ $payment->user?->name ?? 'Guest/Deleted' }}</div>
                                    <div class="text-xs text-gray-500">{{ $payment->user?->email }}</div>
                                </td>
                                <td class="p-3 text-gray-600">
                                    <span class="inline-block px-2 py-0.5 bg-gray-100 text-xs rounded">
                                        {{ class_basename($payment->payable_type) }} #{{ $payment->payable_id }}
                                    </span>
                                </td>
                                <td class="p-3 font-semibold text-gray-900">
                                    ${{ number_format($payment->total_amount, 2) }}
                                    <span class="text-xs text-gray-400 font-normal">{{ $payment->currency }}</span>
                                </td>
                                <td class="p-3 uppercase text-xs font-semibold text-gray-600">
                                    {{ $payment->method?->value ?? $payment->method }}
                                </td>
                                <td class="p-3">
                                    <span class="px-2 py-1 text-xs rounded-full font-semibold badge-{{ $payment->status?->value ?? $payment->status }}">
                                        {{ ucfirst($payment->status?->value ?? $payment->status) }}
                                    </span>
                                </td>
                                <td class="p-3 text-xs text-gray-500">
                                    {{ $payment->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="p-3 text-right">
                                    <a href="{{ route('payments.show', $payment) }}" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs rounded font-medium">
                                        View
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="p-8 text-center text-gray-400">No payment transactions found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t">
                    {{ $payments->links() }}
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
