<x-admin-layout>
    <x-slot name="title">{{ $tier->exists ? 'Edit Tier' : 'Create Tier' }}</x-slot>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $tier->exists ? "Edit Tier: {$tier->name}" : 'Create Loyalty Tier' }}
            </h2>
            <a href="{{ route('admin.tiers.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm hover:bg-gray-200">
                Back to Tiers
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm border border-gray-100 p-6">
                <form method="POST" action="{{ $tier->exists ? route('admin.tiers.update', $tier) : route('admin.tiers.store') }}" class="space-y-4">
                    @csrf
                    @if($tier->exists)
                        @method('PUT')
                    @endif

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Tier Name *</label>
                            <input type="text" name="name" value="{{ old('name', $tier->name) }}" required placeholder="e.g. Gold Tier" class="w-full border px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Color (Hex)</label>
                            <input type="text" name="color" value="{{ old('color', $tier->color ?? '#8fbd56') }}" placeholder="#8fbd56" class="w-full border px-3 py-2 text-sm font-mono">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Description</label>
                        <textarea name="description" rows="2" class="w-full border px-3 py-2 text-sm">{{ old('description', $tier->description) }}</textarea>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Min. Lifetime Points *</label>
                            <input type="number" name="min_points" value="{{ old('min_points', $tier->min_points ?? 0) }}" required class="w-full border px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Point Multiplier *</label>
                            <input type="number" step="0.05" name="point_multiplier" value="{{ old('point_multiplier', $tier->point_multiplier ?? 1.0) }}" required class="w-full border px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Discount %</label>
                            <input type="number" step="0.5" name="discount_percent" value="{{ old('discount_percent', $tier->discount_percent ?? 0) }}" class="w-full border px-3 py-2 text-sm">
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <a href="{{ route('admin.tiers.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm">Cancel</a>
                        <button type="submit" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm shadow-sm">
                            {{ $tier->exists ? 'Save Tier Changes' : 'Create Tier' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
