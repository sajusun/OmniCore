<x-admin-layout>
    @slot('title')
        Vendor Stores Management
    @endslot
    @slot('header')
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Vendor Stores & Merchant Network
            </h2>
            <a href="{{ route('admin.vendor.payouts.index') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition shadow-sm">
                View Payout Requests &rarr;
            </a>
        </div>
    @endslot

    <div class="max-w-7xl mx-auto mt-8 space-y-6">
        <!-- Metric Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <p class="text-sm font-medium text-gray-500">Total Registered Stores</p>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($totalStores) }}</h3>
            </div>
            <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <p class="text-sm font-medium text-gray-500">Verified Merchants</p>
                <h3 class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($verifiedStores) }}</h3>
            </div>
            <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <p class="text-sm font-medium text-gray-500">Total Vendor Sales Revenue</p>
                <h3 class="text-2xl font-bold text-indigo-600 mt-1">${{ number_format($totalRevenue, 2) }}</h3>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <!-- Filter & Search -->
        <x-card title="Search & Filter Stores">
            <form method="GET" action="{{ route('admin.vendor.stores.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, phone..." class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm">
                </div>
                <div>
                    <select name="status" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Review</option>
                        <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-md text-sm font-medium">Filter</button>
                    <a href="{{ route('admin.vendor.stores.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-md text-sm font-medium">Reset</a>
                </div>
            </form>
        </x-card>

        <!-- Stores Table -->
        <x-card title="Stores Index">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Store Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Owner</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Balance</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Commission %</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Verified</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                        @forelse($stores as $store)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-semibold text-gray-900 dark:text-white">{{ $store->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $store->slug }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $store->user?->name ?? 'N/A' }}
                                    <div class="text-xs text-gray-400">{{ $store->user?->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-emerald-600">
                                    ${{ number_format($store->balance, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    {{ $store->commission_rate }}%
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $store->status === 'active' ? 'bg-green-100 text-green-800' : ($store->status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($store->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($store->is_verified)
                                        <span class="inline-flex items-center text-xs font-medium text-emerald-600">
                                            ✓ Verified
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">Unverified</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <form action="{{ route('admin.vendor.stores.update', $store->id) }}" method="POST" class="flex items-center justify-end gap-2">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" class="rounded border-gray-300 text-xs py-1">
                                            <option value="active" {{ $store->status === 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="suspended" {{ $store->status === 'suspended' ? 'selected' : '' }}>Suspend</option>
                                            <option value="pending" {{ $store->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        </select>
                                        <label class="inline-flex items-center text-xs text-gray-600 dark:text-gray-400">
                                            <input type="checkbox" name="is_verified" value="1" {{ $store->is_verified ? 'checked' : '' }} class="rounded border-gray-300 mr-1">
                                            Verify
                                        </label>
                                        <button type="submit" class="px-2.5 py-1 bg-indigo-600 text-white text-xs rounded hover:bg-indigo-700">Save</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">No vendor stores found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $stores->links() }}
            </div>
        </x-card>
    </div>
</x-admin-layout>
