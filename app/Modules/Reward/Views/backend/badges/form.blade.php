<x-admin-layout>
    <x-slot name="title">{{ $badge->exists ? 'Edit Badge' : 'Create Badge' }}</x-slot>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $badge->exists ? "Edit Badge: {$badge->name}" : 'Create Achievement Badge' }}
            </h2>
            <a href="{{ route('admin.badges.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm hover:bg-gray-200">
                Back to Badges
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                <form method="POST" action="{{ $badge->exists ? route('admin.badges.update', $badge) : route('admin.badges.store') }}" class="space-y-4">
                    @csrf
                    @if($badge->exists)
                        @method('PUT')
                    @endif

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Badge Name *</label>
                            <input type="text" name="name" value="{{ old('name', $badge->name) }}" required placeholder="e.g. 7-Day Streak Master" class="w-full border rounded-md px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Badge Type *</label>
                            <select name="badge_type" class="w-full border rounded-md px-3 py-2 text-sm">
                                <option value="streak" {{ old('badge_type', $badge->badge_type) === 'streak' ? 'selected' : '' }}>Daily Streak</option>
                                <option value="achievement" {{ old('badge_type', $badge->badge_type) === 'achievement' ? 'selected' : '' }}>Achievement</option>
                                <option value="milestone" {{ old('badge_type', $badge->badge_type) === 'milestone' ? 'selected' : '' }}>Milestone</option>
                                <option value="spending" {{ old('badge_type', $badge->badge_type) === 'spending' ? 'selected' : '' }}>Spending / Orders</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Description</label>
                        <textarea name="description" rows="2" class="w-full border rounded-md px-3 py-2 text-sm">{{ old('description', $badge->description) }}</textarea>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Bonus Points Reward</label>
                            <input type="number" name="points_reward" value="{{ old('points_reward', $badge->points_reward ?? 50) }}" class="w-full border rounded-md px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Criteria Type</label>
                            <input type="text" name="criteria_type" value="{{ old('criteria_type', $badge->criteria_type ?? 'checkin_streak') }}" placeholder="e.g. checkin_streak, orders_count" class="w-full border rounded-md px-3 py-2 text-sm font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Threshold Value</label>
                            <input type="number" name="criteria_threshold" value="{{ old('criteria_threshold', $badge->criteria_threshold ?? 7) }}" class="w-full border rounded-md px-3 py-2 text-sm">
                        </div>
                    </div>

                    <div class="pt-2">
                        <label class="inline-flex items-center text-sm font-medium text-gray-700">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $badge->exists ? $badge->is_active : true) ? 'checked' : '' }} class="rounded border-gray-300 text-emerald-600 mr-2">
                            Active Badge
                        </label>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <a href="{{ route('admin.badges.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm">Cancel</a>
                        <button type="submit" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-md text-sm shadow-sm">
                            {{ $badge->exists ? 'Save Changes' : 'Create Badge' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
