<x-admin-layout>
    @slot('title')
        Vendor Payout Requests
    @endslot
    @slot('header')
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Vendor Payout & Disbursement Requests
            </h2>
            <a href="{{ route('admin.vendor.stores.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium transition border border-gray-300 dark:border-gray-700">
                &larr; Back to Stores
            </a>
        </div>
    @endslot

    <div class="max-w-7xl mx-auto mt-8 space-y-6">
        <!-- Metric Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <p class="text-sm font-medium text-gray-500">Pending Payouts</p>
                <h3 class="text-2xl font-bold text-amber-600 mt-1">${{ number_format($pendingAmount, 2) }}</h3>
            </div>
            <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <p class="text-sm font-medium text-gray-500">Completed Disbursements</p>
                <h3 class="text-2xl font-bold text-emerald-600 mt-1">${{ number_format($completedAmount, 2) }}</h3>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800">
                {{ session('error') }}
            </div>
        @endif

        <!-- Payouts Table -->
        <x-card title="Disbursement Audit Trail">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Store</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Requested Date</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                        @forelse($payouts as $payout)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">#{{ $payout->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-semibold text-gray-900 dark:text-white">{{ $payout->vendorStore?->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $payout->vendorStore?->user?->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">
                                    ${{ number_format($payout->amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400 uppercase">
                                    {{ $payout->payout_method }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $payout->status === 'completed' ? 'bg-green-100 text-green-800' : ($payout->status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($payout->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $payout->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-2">
                                    @if($payout->status === 'pending')
                                        <form action="{{ route('admin.vendor.payouts.approve', $payout->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 bg-emerald-600 text-white text-xs rounded hover:bg-emerald-700 font-medium">Approve</button>
                                        </form>
                                        <form action="{{ route('admin.vendor.payouts.reject', $payout->id) }}" method="POST" class="inline" onsubmit="return confirm('Reject this payout request and refund funds to store?');">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 font-medium">Reject</button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-400">Processed</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">No payout requests found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $payouts->links() }}
            </div>
        </x-card>
    </div>
</x-admin-layout>
