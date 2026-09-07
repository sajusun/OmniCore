<x-admin-layout>
    @slot('title')
        Achievement Badges
    @endslot
    @slot('header')
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Gamification Achievement Badges
            </h2>
            <a href="{{ route('admin.reward.tiers.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium transition border border-gray-300 dark:border-gray-700">
                &larr; Back to Tiers
            </a>
        </div>
    @endslot

    <div class="max-w-7xl mx-auto mt-8 space-y-6">
        @if(session('success'))
            <div class="p-4 text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <!-- Create Badge Form -->
        <x-card title="Add New Achievement Badge">
            <form action="{{ route('admin.reward.badges.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Badge Name</label>
                        <input type="text" name="name" required placeholder="e.g. 7-Day Streaker" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Slug</label>
                        <input type="text" name="slug" required placeholder="e.g. 7-day-streaker" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Criteria Type</label>
                        <select name="criteria_type" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm text-sm">
                            <option value="streak">Daily Check-in Streak</option>
                            <option value="points">Total Points Earned</option>
                            <option value="orders">Completed Orders</option>
                            <option value="reviews">Verified Reviews</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Criteria Value</label>
                        <input type="number" name="criteria_value" value="7" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                    <input type="text" name="description" placeholder="Awarded for logging in 7 consecutive days." class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm text-sm">
                </div>

                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 font-medium text-sm">
                    Save Badge
                </button>
            </form>
        </x-card>

        <!-- Badges List -->
        <x-card title="Badges List">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($badges as $badge)
                    <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <div>
                            <h4 class="font-bold text-gray-900 dark:text-white">{{ $badge->name }}</h4>
                            <p class="text-xs text-gray-500">{{ $badge->description }}</p>
                            <div class="mt-2 text-xs font-semibold text-indigo-600">
                                Target: {{ $badge->criteria_value }} {{ ucfirst($badge->criteria_type) }} &bull; {{ $badge->users_count }} Unlocked
                            </div>
                        </div>
                        <form action="{{ route('admin.reward.badges.destroy', $badge->id) }}" method="POST" onsubmit="return confirm('Delete badge?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-red-600 hover:text-red-800">Delete</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </x-card>
    </div>
</x-admin-layout>
