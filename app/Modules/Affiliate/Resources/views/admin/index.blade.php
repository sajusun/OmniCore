<x-admin-layout>
    @slot('title')
        Affiliate Network
    @endslot
    @slot('header')
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Affiliate & Partner Network
            </h2>
            <a href="{{ route('admin.affiliate.commissions.index') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition shadow-sm">
                Commission Logs &rarr;
            </a>
        </div>
    @endslot

    <div class="max-w-7xl mx-auto mt-8 space-y-6">
        <!-- Metric Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <p class="text-sm font-medium text-gray-500">Active Partners</p>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($totalAffiliates) }}</h3>
            </div>
            <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <p class="text-sm font-medium text-gray-500">Total Referral Clicks</p>
                <h3 class="text-2xl font-bold text-indigo-600 mt-1">{{ number_format($totalClicks) }}</h3>
            </div>
            <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <p class="text-sm font-medium text-gray-500">Commissions Disbursed</p>
                <h3 class="text-2xl font-bold text-emerald-600 mt-1">${{ number_format($totalPaidCommissions, 2) }}</h3>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <!-- Affiliates Table -->
        <x-card title="Affiliate Partners List">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Partner</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Referral Code / Slug</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Clicks</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Conversions</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Balance</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Custom Rate</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                        @forelse($affiliates as $affiliate)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-semibold text-gray-900 dark:text-white">{{ $affiliate->user?->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $affiliate->user?->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="font-mono bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded text-xs">{{ $affiliate->referral_code }}</span>
                                    @if($affiliate->custom_slug)
                                        <div class="text-xs text-indigo-600 mt-0.5">/ref/{{ $affiliate->custom_slug }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ number_format($affiliate->total_clicks) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ number_format($affiliate->total_conversions) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-emerald-600">
                                    ${{ number_format($affiliate->balance, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    {{ $affiliate->custom_commission_rate ? $affiliate->custom_commission_rate . '%' : 'Default (10%)' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <form action="{{ route('admin.affiliate.update', $affiliate->id) }}" method="POST" class="flex items-center justify-end gap-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="number" step="0.5" name="custom_commission_rate" value="{{ $affiliate->custom_commission_rate }}" placeholder="Rate %" class="w-20 rounded border-gray-300 text-xs py-1">
                                        <select name="status" class="rounded border-gray-300 text-xs py-1">
                                            <option value="active" {{ $affiliate->status === 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="suspended" {{ $affiliate->status === 'suspended' ? 'selected' : '' }}>Suspend</option>
                                        </select>
                                        <button type="submit" class="px-2.5 py-1 bg-indigo-600 text-white text-xs rounded hover:bg-indigo-700">Save</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">No affiliate accounts found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $affiliates->links() }}
            </div>
        </x-card>
    </div>
</x-admin-layout>
