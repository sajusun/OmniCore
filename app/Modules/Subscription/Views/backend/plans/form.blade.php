<x-admin-layout>
    <x-slot name="title">{{ $plan->exists ? 'Edit Plan' : 'Create Plan' }}</x-slot>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $plan->exists ? "Edit Plan: {$plan->name}" : 'Create New Subscription Plan' }}
            </h2>
            <a href="{{ route('admin.plans.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm hover:bg-gray-200">
                Back to Plans
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm border border-gray-100 p-6">
                <form method="POST" action="{{ $plan->exists ? route('admin.plans.update', $plan) : route('admin.plans.store') }}" class="space-y-6">
                    @csrf
                    @if($plan->exists)
                        @method('PUT')
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Plan Name *</label>
                            <input type="text" name="name" value="{{ old('name', $plan->name) }}" required placeholder="e.g. Pro Monthly" class="w-full border px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Slug (optional)</label>
                            <input type="text" name="slug" value="{{ old('slug', $plan->slug) }}" placeholder="e.g. pro-monthly" class="w-full border px-3 py-2 text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Description</label>
                        <textarea name="description" rows="2" placeholder="Brief summary of plan benefits" class="w-full border px-3 py-2 text-sm">{{ old('description', $plan->description) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Price *</label>
                            <input type="number" step="0.01" name="price" value="{{ old('price', $plan->price ?? 0) }}" required class="w-full border px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Signup Fee</label>
                            <input type="number" step="0.01" name="signup_fee" value="{{ old('signup_fee', $plan->signup_fee ?? 0) }}" class="w-full border px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Billing Interval *</label>
                            <select name="billing_interval" class="w-full border px-3 py-2 text-sm">
                                <option value="month" {{ old('billing_interval', $plan->billing_interval) === 'month' ? 'selected' : '' }}>Monthly</option>
                                <option value="year" {{ old('billing_interval', $plan->billing_interval) === 'year' ? 'selected' : '' }}>Yearly</option>
                                <option value="week" {{ old('billing_interval', $plan->billing_interval) === 'week' ? 'selected' : '' }}>Weekly</option>
                                <option value="day" {{ old('billing_interval', $plan->billing_interval) === 'day' ? 'selected' : '' }}>Daily</option>
                                <option value="lifetime" {{ old('billing_interval', $plan->billing_interval) === 'lifetime' ? 'selected' : '' }}>Lifetime</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Trial Days</label>
                            <input type="number" name="trial_days" value="{{ old('trial_days', $plan->trial_days ?? 0) }}" class="w-full border px-3 py-2 text-sm">
                        </div>
                    </div>

                    <div class="flex items-center gap-6 pt-2">
                        <label class="inline-flex items-center text-sm font-medium text-gray-700">
                            <input type="checkbox" name="is_popular" value="1" {{ old('is_popular', $plan->is_popular) ? 'checked' : '' }} class="border-gray-300 text-emerald-600 mr-2">
                            Mark as Popular Plan
                        </label>
                        <label class="inline-flex items-center text-sm font-medium text-gray-700">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $plan->exists ? $plan->is_active : true) ? 'checked' : '' }} class="border-gray-300 text-emerald-600 mr-2">
                            Active & Publicly Available
                        </label>
                    </div>

                    <!-- Features Matrix Builder -->
                    <div class="pt-6 border-t">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="font-bold text-gray-900 text-sm uppercase">Plan Features & Quotas</h3>
                            <button type="button" onclick="addFeatureRow()" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold">
                                + Add Feature
                            </button>
                        </div>

                        <div id="features-container" class="space-y-3">
                            @php
                                $features = old('features', $plan->features ?? []);
                            @endphp
                            @forelse($features as $i => $feat)
                            <div class="grid grid-cols-12 gap-2 items-center feature-row bg-gray-50 p-2">
                                <div class="col-span-4">
                                    <input type="text" name="features[{{ $i }}][name]" value="{{ is_array($feat) ? ($feat['name'] ?? '') : $feat->name }}" placeholder="Feature Name" class="w-full border px-2.5 py-1 text-xs">
                                </div>
                                <div class="col-span-3">
                                    <input type="text" name="features[{{ $i }}][code]" value="{{ is_array($feat) ? ($feat['code'] ?? '') : $feat->code }}" placeholder="code_name" class="w-full border px-2.5 py-1 text-xs font-mono">
                                </div>
                                <div class="col-span-3">
                                    <input type="text" name="features[{{ $i }}][value]" value="{{ is_array($feat) ? ($feat['value'] ?? '') : $feat->value }}" placeholder="e.g. true or 100" class="w-full border px-2.5 py-1 text-xs">
                                </div>
                                <div class="col-span-2 text-right">
                                    <button type="button" onclick="this.closest('.feature-row').remove()" class="text-rose-600 hover:underline text-xs">Remove</button>
                                </div>
                            </div>
                            @empty
                            <div class="grid grid-cols-12 gap-2 items-center feature-row bg-gray-50 p-2">
                                <div class="col-span-4">
                                    <input type="text" name="features[0][name]" value="Unlimited Direct Chat" class="w-full border px-2.5 py-1 text-xs">
                                </div>
                                <div class="col-span-3">
                                    <input type="text" name="features[0][code]" value="unlimited_chat" class="w-full border px-2.5 py-1 text-xs font-mono">
                                </div>
                                <div class="col-span-3">
                                    <input type="text" name="features[0][value]" value="true" class="w-full border px-2.5 py-1 text-xs">
                                </div>
                                <div class="col-span-2 text-right">
                                    <button type="button" onclick="this.closest('.feature-row').remove()" class="text-rose-600 hover:underline text-xs">Remove</button>
                                </div>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="pt-6 border-t flex justify-end gap-3">
                        <a href="{{ route('admin.plans.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm">Cancel</a>
                        <button type="submit" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm shadow-sm">
                            {{ $plan->exists ? 'Save Plan Changes' : 'Create Subscription Plan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let featureIdx = 100;
        function addFeatureRow() {
            featureIdx++;
            const container = document.getElementById('features-container');
            const row = document.createElement('div');
            row.className = 'grid grid-cols-12 gap-2 items-center feature-row bg-gray-50 p-2';
            row.innerHTML = `
                <div class="col-span-4">
                    <input type="text" name="features[${featureIdx}][name]" placeholder="Feature Name" class="w-full border px-2.5 py-1 text-xs">
                </div>
                <div class="col-span-3">
                    <input type="text" name="features[${featureIdx}][code]" placeholder="code_name" class="w-full border px-2.5 py-1 text-xs font-mono">
                </div>
                <div class="col-span-3">
                    <input type="text" name="features[${featureIdx}][value]" placeholder="e.g. true or 100" class="w-full border px-2.5 py-1 text-xs">
                </div>
                <div class="col-span-2 text-right">
                    <button type="button" onclick="this.closest('.feature-row').remove()" class="text-rose-600 hover:underline text-xs">Remove</button>
                </div>
            `;
            container.appendChild(row);
        }
    </script>
    @endpush
</x-admin-layout>
