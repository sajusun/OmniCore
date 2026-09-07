<x-admin-layout>
    @slot('title')
        Loyalty Leaderboard
    @endslot
    @slot('header')
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                User Streaks & Points Leaderboard
            </h2>
            <a href="{{ route('admin.reward.tiers.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium transition border border-gray-300 dark:border-gray-700">
                &larr; Back to Tiers
            </a>
        </div>
    @endslot

    <div class="max-w-7xl mx-auto mt-8 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Points Leaderboard -->
            <x-card title="Top Point Earners">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Rank</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">User</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Points</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                            @foreach($topUsers as $userReward)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-bold text-gray-500">#{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">{{ $userReward->user?->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-sm font-bold text-indigo-600 text-right">{{ number_format($userReward->current_points) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>

            <!-- Streak Leaderboard -->
            <x-card title="Top Check-in Streaks">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Rank</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">User</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Streak</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                            @foreach($topStreaks as $userReward)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-bold text-gray-500">#{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">{{ $userReward->user?->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-sm font-bold text-amber-500 text-right">🔥 {{ $userReward->streak_days }} Days</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>
</x-admin-layout>
