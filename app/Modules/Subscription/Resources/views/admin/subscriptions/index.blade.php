<x-admin-layout>
    @slot('title')
        Active Subscriptions
    @endslot
    @slot('header')
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Active Member Subscriptions
            </h2>
            <a href="{{ route('admin.subscription.plans.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium transition border border-gray-300 dark:border-gray-700">
                &larr; Manage Plans
            </a>
        </div>
    @endslot

    <div class="max-w-7xl mx-auto mt-8 space-y-6">
        <x-card title="Subscribers List">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Started At</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expires At</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Auto Renew</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                        @forelse($subscriptions as $sub)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-semibold text-gray-900 dark:text-white">{{ $sub->user?->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $sub->user?->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-bold text-indigo-600">{{ $sub->plan?->name ?? 'Custom Plan' }}</span>
                                    <div class="text-xs text-gray-500">${{ number_format($sub->plan?->price ?? 0, 2) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sub->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($sub->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $sub->starts_at?->format('M d, Y') ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $sub->expires_at?->format('M d, Y') ?? 'Lifetime' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ $sub->auto_renew ? '✓ Yes' : '✕ No' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No active subscribers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $subscriptions->links() }}
            </div>
        </x-card>
    </div>
</x-admin-layout>
