<x-admin-layout>
    <x-slot name="title">Payment Gateways</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Payment Gateway Configuration</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm border border-green-200 mb-6">
                {{ session('success') }}
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($gateways as $gateway)
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center font-bold text-gray-700 text-sm uppercase">
                                    {{ substr($gateway->code, 0, 2) }}
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900 text-base">{{ $gateway->name }}</h3>
                                    <span class="text-xs text-gray-500 font-mono">{{ $gateway->code }}</span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $gateway->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $gateway->is_active ? 'Active' : 'Disabled' }}
                            </span>
                        </div>

                        <p class="text-xs text-gray-600 mb-4">{{ $gateway->description }}</p>

                        <form method="POST" action="{{ route('gateways.update', $gateway) }}" class="space-y-3">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Display Name</label>
                                    <input type="text" name="name" value="{{ old('name', $gateway->name) }}" class="w-full border rounded px-3 py-1.5 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Mode</label>
                                    <select name="is_sandbox" class="w-full border rounded px-3 py-1.5 text-sm">
                                        <option value="1" {{ $gateway->is_sandbox ? 'selected' : '' }}>Sandbox / Test</option>
                                        <option value="0" {{ !$gateway->is_sandbox ? 'selected' : '' }}>Live / Production</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Fixed Fee</label>
                                    <input type="number" step="0.01" name="fee_fixed" value="{{ old('fee_fixed', $gateway->fee_fixed) }}" class="w-full border rounded px-3 py-1.5 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Percentage Fee (%)</label>
                                    <input type="number" step="0.01" name="fee_percent" value="{{ old('fee_percent', $gateway->fee_percent) }}" class="w-full border rounded px-3 py-1.5 text-sm">
                                </div>
                            </div>

                            @if(is_array($gateway->credentials) && count($gateway->credentials) > 0)
                            <div class="pt-2 border-t">
                                <label class="block text-xs font-semibold text-gray-700 uppercase mb-2">API Credentials</label>
                                <div class="space-y-2">
                                    @foreach($gateway->credentials as $cKey => $cVal)
                                    <div>
                                        <label class="block text-[11px] text-gray-500 font-mono">{{ $cKey }}</label>
                                        <input type="text" name="credentials[{{ $cKey }}]" value="{{ $cVal }}" class="w-full border rounded px-2.5 py-1 text-xs font-mono">
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <div class="flex items-center justify-between pt-4 border-t">
                                <label class="inline-flex items-center text-xs font-medium text-gray-700">
                                    <input type="checkbox" name="is_active" value="1" {{ $gateway->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 mr-2">
                                    Enable Gateway
                                </label>
                                <button type="submit" class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-xs font-semibold">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</x-admin-layout>
