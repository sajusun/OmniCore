<x-admin-layout>
    <x-slot name="title">Achievement Badges</x-slot>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Achievement & Milestone Badges</h2>
            <a href="{{ route('admin.badges.create') }}" class="px-4 py-2 bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700">
                + Create New Badge
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
            <div class="p-4 bg-green-50 text-green-700 text-sm border border-green-200">
                {{ session('success') }}
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                @forelse($badges as $badge)
                <div class="bg-white shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-3">
                            <span class="w-10 h-10 bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">
                                🏆
                            </span>
                            <span class="px-2 py-0.5 text-xs font-semibold {{ $badge->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $badge->is_active ? 'Active' : 'Disabled' }}
                            </span>
                        </div>

                        <h3 class="font-bold text-gray-900 text-base mb-1">{{ $badge->name }}</h3>
                        <p class="text-xs text-gray-500 mb-4 min-h-[32px]">{{ $badge->description }}</p>

                        <div class="space-y-1.5 border-t pt-3 mb-6 text-xs">
                            <div class="flex justify-between text-gray-600">
                                <span>Type:</span>
                                <span class="font-semibold uppercase text-gray-800">{{ $badge->badge_type }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Bonus Reward:</span>
                                <span class="font-bold text-emerald-600">+{{ $badge->points_reward }} pts</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Unlocked By:</span>
                                <span class="font-bold text-gray-800">{{ $badge->users_count }} users</span>
                            </div>
                        </div>
                    </div>

                    <div class="border-t pt-4 flex justify-between items-center text-xs">
                        <a href="{{ route('admin.badges.edit', $badge) }}" class="text-emerald-600 font-bold hover:underline">Edit Badge</a>
                        <form method="POST" action="{{ route('admin.badges.destroy', $badge) }}" onsubmit="return confirm('Delete this badge?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-600 hover:underline">Delete</button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="col-span-4 bg-white p-8 text-center text-gray-400 border border-gray-100">
                    No achievement badges configured yet.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-admin-layout>
