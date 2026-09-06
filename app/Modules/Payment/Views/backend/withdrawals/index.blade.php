<x-admin-layout>
    <x-slot name="title">Withdrawal Requests</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">User Withdrawal Requests</h2>
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

            <!-- Stats Row -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Pending Requests</p>
                    <p class="text-2xl font-bold text-amber-500 mt-1">{{ number_format($stats['pending_count']) }}</p>
                </div>
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Pending Amount</p>
                    <p class="text-2xl font-bold text-amber-600 mt-1">${{ number_format($stats['pending_amount'], 2) }}</p>
                </div>
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Disbursed Requests</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($stats['approved_count']) }}</p>
                </div>
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Disbursed Amount</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">${{ number_format($stats['approved_amount'], 2) }}</p>
                </div>
            </div>

            <!-- Filter -->
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 mb-6">
                <form method="GET" action="{{ route('withdrawals.index') }}" class="flex gap-4">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search user name or email..." class="flex-1 px-3 py-2 border rounded-md text-sm">
                    <select name="status" class="px-3 py-2 border rounded-md text-sm">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md text-sm font-semibold hover:bg-emerald-700">Filter</button>
                    <a href="{{ route('withdrawals.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm hover:bg-gray-200">Reset</a>
                </form>
            </div>

            <!-- Withdrawals Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead class="bg-gray-50 border-b text-gray-600 uppercase text-xs">
                            <tr>
                                <th class="p-3">ID</th>
                                <th class="p-3">User</th>
                                <th class="p-3">Method</th>
                                <th class="p-3">Account Details</th>
                                <th class="p-3">Amount</th>
                                <th class="p-3">Status</th>
                                <th class="p-3">Requested At</th>
                                <th class="p-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($withdrawals as $withdrawal)
                            <tr class="hover:bg-gray-50">
                                <td class="p-3 font-mono font-bold text-gray-700">#{{ $withdrawal->id }}</td>
                                <td class="p-3">
                                    <div class="font-medium text-gray-900">{{ $withdrawal->user?->name ?? 'User #' . $withdrawal->user_id }}</div>
                                    <div class="text-xs text-gray-500">{{ $withdrawal->user?->email }}</div>
                                </td>
                                <td class="p-3 uppercase font-semibold text-xs text-gray-700">
                                    {{ $withdrawal->method }}
                                </td>
                                <td class="p-3 text-xs text-gray-600">
                                    @if(is_array($withdrawal->account_details))
                                        @foreach($withdrawal->account_details as $key => $val)
                                            <div><strong class="text-gray-700">{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $val }}</div>
                                        @endforeach
                                    @else
                                        {{ $withdrawal->account_details }}
                                    @endif
                                </td>
                                <td class="p-3 font-bold text-gray-900">
                                    ${{ number_format($withdrawal->payable_amount, 2) }}
                                    <span class="text-xs text-gray-400 font-normal">{{ $withdrawal->currency }}</span>
                                </td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full
                                        {{ $withdrawal->status?->value === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                                        {{ $withdrawal->status?->value === 'pending' ? 'bg-amber-100 text-amber-700' : '' }}
                                        {{ $withdrawal->status?->value === 'rejected' ? 'bg-rose-100 text-rose-700' : '' }}
                                        {{ $withdrawal->status?->value === 'cancelled' ? 'bg-gray-100 text-gray-700' : '' }}">
                                        {{ ucfirst($withdrawal->status?->value ?? $withdrawal->status) }}
                                    </span>
                                </td>
                                <td class="p-3 text-xs text-gray-500">
                                    {{ $withdrawal->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="p-3 text-right space-x-2">
                                    @if($withdrawal->status?->value === 'pending')
                                        <!-- Approve -->
                                        <form method="POST" action="{{ route('withdrawals.approve', $withdrawal) }}" class="inline" onsubmit="return confirm('Approve and confirm disbursement for this request?')">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 bg-emerald-600 text-white rounded text-xs font-semibold hover:bg-emerald-700">
                                                Approve
                                            </button>
                                        </form>

                                        <!-- Reject Trigger -->
                                        <button onclick="document.getElementById('reject-modal-{{ $withdrawal->id }}').classList.remove('hidden')" class="px-3 py-1 bg-rose-600 text-white rounded text-xs font-semibold hover:bg-rose-700">
                                            Reject & Refund
                                        </button>

                                        <!-- Reject Modal -->
                                        <div id="reject-modal-{{ $withdrawal->id }}" class="fixed inset-0 bg-black/40 hidden z-50 flex items-center justify-center text-left">
                                            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4 shadow-xl">
                                                <h3 class="font-bold text-gray-900 text-base mb-2">Reject Withdrawal #{{ $withdrawal->id }}</h3>
                                                <p class="text-xs text-gray-500 mb-4">The funds (${{ number_format($withdrawal->amount, 2) }}) will be immediately credited back to the user's wallet.</p>
                                                
                                                <form method="POST" action="{{ route('withdrawals.reject', $withdrawal) }}" class="space-y-4">
                                                    @csrf
                                                    <div>
                                                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Rejection Reason</label>
                                                        <input type="text" name="rejection_reason" required placeholder="e.g. Invalid account details, KYC required" class="w-full border rounded-md px-3 py-2 text-sm">
                                                    </div>
                                                    <div class="flex justify-end gap-2 pt-2">
                                                        <button type="button" onclick="document.getElementById('reject-modal-{{ $withdrawal->id }}').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm">Cancel</button>
                                                        <button type="submit" class="px-4 py-2 bg-rose-600 text-white rounded-md text-sm font-semibold hover:bg-rose-700">Confirm Rejection</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400">Processed</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="p-8 text-center text-gray-400">No withdrawal requests found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t">
                    {{ $withdrawals->links() }}
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
