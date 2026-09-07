<x-admin-layout>
    @slot('title')
        Reward Tiers & Points
    @endslot
    @slot('header')
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Loyalty Reward Tiers & Multipliers
            </h2>
            <div class="space-x-2">
                <a href="{{ route('admin.reward.badges.index') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition shadow-sm">
                    Manage Badges
                </a>
                <a href="{{ route('admin.reward.leaderboard.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium transition border border-gray-300 dark:border-gray-700">
                    Leaderboard &rarr;
                </a>
            </div>
        </div>
    @endslot

    <div class="max-w-7xl mx-auto mt-8 space-y-6">
        <!-- Metric Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <p class="text-sm font-medium text-gray-500">Total Points Awarded</p>
                <h3 class="text-2xl font-bold text-indigo-600 mt-1">{{ number_format($totalPointsIssued) }} pts</h3>
            </div>
            <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <p class="text-sm font-medium text-gray-500">Total Points Redeemed to Cash</p>
                <h3 class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format(abs($totalRedeemedPoints)) }} pts</h3>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <!-- Tiers Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            @foreach($tiers as $tier)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-4">
                    <form action="{{ route('admin.reward.tiers.update', $tier->id) }}" method="POST" class="space-y-3">
                        @csrf
                        @method('PUT')

                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-indigo-600">Level {{ $loop->iteration }}</span>
                            <input type="text" name="name" value="{{ $tier->name }}" class="mt-1 block w-full font-bold text-gray-900 dark:text-white rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm">
                        </div>

                        <div>
                            <label class="block text-xs text-gray-500">Min Points Required</label>
                            <input type="number" name="min_points" value="{{ $tier->min_points }}" class="mt-1 block w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-xs">
                        </div>

                        <div>
                            <label class="block text-xs text-gray-500">Point Multiplier (e.g. 1.25x)</label>
                            <input type="number" step="0.05" name="point_multiplier" value="{{ $tier->point_multiplier }}" class="mt-1 block w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-xs">
                        </div>

                        <div>
                            <label class="block text-xs text-gray-500">Cashback %</label>
                            <input type="number" step="0.5" name="cashback_percentage" value="{{ $tier->cashback_percentage }}" class="mt-1 block w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-xs">
                        </div>

                        <button type="submit" class="w-full py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-xs font-semibold">
                            Update Tier
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
</x-admin-layout>
