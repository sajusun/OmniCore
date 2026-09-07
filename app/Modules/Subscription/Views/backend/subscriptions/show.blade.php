<x-admin-layout>
    <x-slot name="title">Subscription #{{ $subscription->id }}</x-slot>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Subscriber Details: {{ $subscription->user?->name }}</h2>
            <a href="{{ route('admin.subscriptions.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm hover:bg-gray-200">
                Back to List
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Subscription Info -->
            <div class="bg-white shadow-sm border border-gray-100 p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-base font-bold text-gray-900 mb-4">Subscription Overview</h3>
                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-between border-b pb-2">
                                <dt class="text-gray-500">Plan:</dt>
                                <dd class="font-bold text-gray-800">{{ $subscription->plan?->name }}</dd>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <dt class="text-gray-500">Status:</dt>
                                <dd class="font-bold uppercase text-xs px-2 py-0.5 {{ $subscription->isActive() ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $subscription->status?->value ?? $subscription->status }}
                                </dd>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <dt class="text-gray-500">Billing Interval:</dt>
                                <dd class="text-gray-800 uppercase text-xs font-semibold">{{ $subscription->plan?->billing_interval }}</dd>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <dt class="text-gray-500">Started At:</dt>
                                <dd class="text-gray-800">{{ $subscription->starts_at ? $subscription->starts_at->format('M d, Y H:i') : 'N/A' }}</dd>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <dt class="text-gray-500">Ends / Renews At:</dt>
                                <dd class="text-gray-800">{{ $subscription->ends_at ? $subscription->ends_at->format('M d, Y H:i') : 'Lifetime' }}</dd>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <dt class="text-gray-500">Payment Method:</dt>
                                <dd class="text-gray-800 uppercase text-xs">{{ $subscription->payment_method ?: 'N/A' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h3 class="text-base font-bold text-gray-900 mb-4">User Details</h3>
                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-between border-b pb-2">
                                <dt class="text-gray-500">Customer Name:</dt>
                                <dd class="font-semibold text-gray-800">{{ $subscription->user?->name }}</dd>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <dt class="text-gray-500">Email:</dt>
                                <dd class="text-gray-800">{{ $subscription->user?->email }}</dd>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <dt class="text-gray-500">User ID:</dt>
                                <dd class="font-mono text-gray-800">#{{ $subscription->user_id }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Feature Usages & Quotas -->
            <div class="bg-white shadow-sm border border-gray-100 p-6">
                <h3 class="text-base font-bold text-gray-900 mb-4">Feature Quota & Usage Ledger</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead class="bg-gray-50 border-b text-gray-600 uppercase">
                            <tr>
                                <th class="p-3">Feature Code</th>
                                <th class="p-3">Quota</th>
                                <th class="p-3">Consumed</th>
                                <th class="p-3">Remaining</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($subscription->plan?->features ?? [] as $feat)
                            @php
                                $usage = $subscription->usages->firstWhere('feature_code', $feat->code);
                                $used = $usage?->used ?? 0;
                                $quota = $feat->getQuota();
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="p-3 font-mono font-medium text-gray-800">{{ $feat->name }} ({{ $feat->code }})</td>
                                <td class="p-3">{{ $feat->isUnlimited() ? 'Unlimited' : $quota }}</td>
                                <td class="p-3 font-semibold text-gray-900">{{ $used }}</td>
                                <td class="p-3 font-bold {{ $feat->isUnlimited() || ($quota - $used > 0) ? 'text-green-600' : 'text-rose-600' }}">
                                    {{ $feat->isUnlimited() ? 'Unlimited' : max(0, $quota - $used) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="p-4 text-center text-gray-400">No feature quotas configured.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
