<x-admin-layout>
    <x-slot name="title">Subscription Plans</x-slot>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Membership & Pricing Plans</h2>
            <a href="{{ route('admin.plans.create') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-md text-sm font-semibold hover:bg-emerald-700">
                + Create New Plan
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
            <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm border border-green-200">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="p-4 bg-red-50 text-red-700 rounded-lg text-sm border border-red-200">
                {{ session('error') }}
            </div>
            @endif

            <!-- Plans Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                @forelse($plans as $plan)
                <div class="bg-white rounded-lg shadow-sm border {{ $plan->is_popular ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-gray-100' }} p-6 flex flex-col justify-between relative">
                    @if($plan->is_popular)
                    <span class="absolute -top-3 right-4 px-3 py-0.5 bg-emerald-500 text-white text-[11px] uppercase font-bold rounded-full shadow-sm">
                        Popular
                    </span>
                    @endif

                    <div>
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="font-bold text-gray-900 text-lg">{{ $plan->name }}</h3>
                            <span class="px-2 py-0.5 text-xs font-semibold rounded {{ $plan->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $plan->is_active ? 'Active' : 'Disabled' }}
                            </span>
                        </div>

                        <p class="text-xs text-gray-500 mb-4 min-h-[32px]">{{ $plan->description }}</p>

                        <div class="mb-4">
                            <span class="text-3xl font-bold text-gray-900">${{ number_format($plan->price, 2) }}</span>
                            <span class="text-xs text-gray-500">/ {{ $plan->billing_interval }}</span>
                            @if($plan->trial_days > 0)
                            <div class="text-xs text-emerald-600 font-semibold mt-1">{{ $plan->trial_days }} Days Free Trial</div>
                            @endif
                        </div>

                        <!-- Feature Checklist -->
                        <div class="border-t pt-3 space-y-2 mb-6">
                            <h4 class="text-xs uppercase font-semibold text-gray-400 tracking-wider">Features Included</h4>
                            @foreach($plan->features as $feat)
                            <div class="flex items-center text-xs text-gray-700">
                                <svg class="w-4 h-4 text-emerald-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>{{ $feat->name }} (<strong>{{ $feat->value }}</strong>)</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="border-t pt-4 flex justify-between items-center text-xs">
                        <a href="{{ route('admin.plans.edit', $plan) }}" class="text-emerald-600 font-bold hover:underline">
                            Edit Plan
                        </a>

                        <form method="POST" action="{{ route('admin.plans.destroy', $plan) }}" onsubmit="return confirm('Delete this plan?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-600 hover:underline">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="col-span-4 bg-white p-8 rounded-lg text-center text-gray-400">
                    No subscription plans configured yet.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-admin-layout>
