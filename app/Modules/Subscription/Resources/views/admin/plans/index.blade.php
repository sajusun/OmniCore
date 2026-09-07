<x-admin-layout>
    @slot('title')
        Subscription Plans & Quotas
    @endslot
    @slot('header')
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Subscription Plans & Feature Quotas
            </h2>
            <a href="{{ route('admin.subscription.subscriptions.index') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition shadow-sm">
                Active Subscribers &rarr;
            </a>
        </div>
    @endslot

    <div class="max-w-7xl mx-auto mt-8 space-y-6">
        <!-- Metric Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <p class="text-sm font-medium text-gray-500">Active Subscribers</p>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($totalSubscribers) }}</h3>
            </div>
            <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <p class="text-sm font-medium text-gray-500">Estimated MRR</p>
                <h3 class="text-2xl font-bold text-emerald-600 mt-1">${{ number_format($monthlyRevenue, 2) }}</h3>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <!-- Create Plan Form -->
        <x-card title="Create New Subscription Plan">
            <form action="{{ route('admin.subscription.plans.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Plan Name</label>
                        <input type="text" name="name" required placeholder="e.g. Pro Tier, Enterprise" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Price (USD)</label>
                        <input type="number" step="0.01" name="price" required placeholder="29.99 (0 for free)" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Duration (Days)</label>
                        <input type="number" name="duration_days" value="30" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description / Highlights</label>
                    <textarea name="description" rows="2" placeholder="Key benefits of this plan..." class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm text-sm"></textarea>
                </div>

                <div class="flex items-center gap-4">
                    <label class="inline-flex items-center text-sm text-gray-700 dark:text-gray-300">
                        <input type="checkbox" name="is_featured" value="1" class="rounded border-gray-300 mr-2">
                        Mark as Featured / Best Value
                    </label>
                </div>

                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 font-medium text-sm">
                    Save Plan
                </button>
            </form>
        </x-card>

        <!-- Plans Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($plans as $plan)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border {{ $plan->is_featured ? 'border-indigo-500 ring-2 ring-indigo-500/20' : 'border-gray-200 dark:border-gray-700' }} p-6 space-y-4 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $plan->name }}</h3>
                            @if($plan->is_featured)
                                <span class="px-2 py-0.5 text-xs font-semibold text-indigo-700 bg-indigo-100 rounded-full">Featured</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $plan->description ?? 'No description provided.' }}</p>

                        <div class="mt-4 flex items-baseline">
                            <span class="text-3xl font-extrabold text-gray-900 dark:text-white">${{ number_format($plan->price, 2) }}</span>
                            <span class="text-xs text-gray-500 ml-1">/ {{ $plan->duration_days }} days</span>
                        </div>

                        <!-- Quotas -->
                        <div class="mt-5 space-y-2 border-t border-gray-100 dark:border-gray-700 pt-4">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Features & Quotas</p>
                            @forelse($plan->features as $feat)
                                <div class="flex items-center justify-between text-xs text-gray-700 dark:text-gray-300">
                                    <span>{{ str_replace('_', ' ', ucfirst($feat->feature_key)) }}</span>
                                    <span class="font-bold text-indigo-600">{{ $feat->quota_limit ?? 'Unlimited' }}</span>
                                </div>
                            @empty
                                <p class="text-xs text-gray-400">Standard access.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="border-t border-gray-100 dark:border-gray-700 pt-4 flex items-center justify-between">
                        <span class="text-xs text-gray-500">{{ $plan->subscriptions->count() }} active users</span>
                        <form action="{{ route('admin.subscription.plans.destroy', $plan->id) }}" method="POST" onsubmit="return confirm('Delete this plan?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-red-600 hover:text-red-800 font-medium">Delete</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-admin-layout>
