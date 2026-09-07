<x-admin-layout>
    <x-slot name="title">Loyalty Tiers</x-slot>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Reward & Loyalty Tiers</h2>
            <a href="{{ route('admin.tiers.create') }}" class="px-4 py-2 bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700">
                + Create New Tier
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
                @forelse($tiers as $tier)
                <div class="bg-white shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-center mb-3">
                            <span class="w-8 h-8 flex items-center justify-center font-bold text-white text-xs" style="background-color: {{ $tier->color }}">
                                {{ substr($tier->name, 0, 1) }}
                            </span>
                            <span class="px-2 py-0.5 text-xs font-semibold bg-gray-100 text-gray-700 font-mono">
                                {{ number_format($tier->min_points) }}+ pts
                            </span>
                        </div>

                        <h3 class="font-bold text-gray-900 text-lg mb-1">{{ $tier->name }}</h3>
                        <p class="text-xs text-gray-500 mb-4">{{ $tier->description }}</p>

                        <div class="space-y-2 border-t pt-3 mb-6 text-xs text-gray-700">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Point Multiplier:</span>
                                <span class="font-bold text-emerald-600">{{ $tier->point_multiplier }}x</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Store Discount:</span>
                                <span class="font-bold text-emerald-600">{{ $tier->discount_percent }}%</span>
                            </div>
                            @if(!empty($tier->perks))
                            <div class="pt-2 border-t">
                                <span class="text-[11px] uppercase font-semibold text-gray-400">Perks:</span>
                                <ul class="list-disc list-inside mt-1 space-y-1 text-gray-600">
                                    @foreach($tier->perks as $perk)
                                    <li>{{ $perk }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="border-t pt-4 flex justify-between items-center text-xs">
                        <a href="{{ route('admin.tiers.edit', $tier) }}" class="text-emerald-600 font-bold hover:underline">Edit Tier</a>
                        <form method="POST" action="{{ route('admin.tiers.destroy', $tier) }}" onsubmit="return confirm('Delete this tier?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-600 hover:underline">Delete</button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="col-span-4 bg-white p-8 text-center text-gray-400 border border-gray-100">
                    No reward tiers configured yet.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-admin-layout>
