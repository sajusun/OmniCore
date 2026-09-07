<x-admin-layout>
    <x-slot name="title">Payment Gateways</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Payment Gateway Configuration</h2>
    </x-slot>

    <div class="container-fluid py-4">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1"><i class="bi bi-credit-card me-2 text-primary"></i> Payment Gateways & Processors</h4>
                <p class="text-muted small mb-0">Enable, disable, and configure credentials and transaction processing fees for active payment methods.</p>
            </div>
            <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-primary px-3">
                <i class="bi bi-clock-history me-1"></i> Payment Logs
            </a>
        </div>

        {{-- Status Notification Modal --}}
        <x-modal.status />

        {{-- Gateways Grid --}}
        <div class="row g-4">
            @forelse($gateways as $gateway)
                <div class="col-md-6">
                    <x-card class="h-100">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-light border d-flex align-items-center justify-content-center fw-bold text-dark text-uppercase shadow-sm" style="width: 44px; height: 44px;">
                                    {{ substr($gateway->code, 0, 2) }}
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0 text-dark">{{ $gateway->name }}</h5>
                                    <span class="text-muted small font-monospace">{{ $gateway->code }}</span>
                                </div>
                            </div>
                            @if($gateway->is_active)
                                <x-badge color="success">Active</x-badge>
                            @else
                                <x-badge color="secondary">Disabled</x-badge>
                            @endif
                        </div>

                        <p class="text-muted small mb-3">{{ $gateway->description }}</p>

                        <form method="POST" action="{{ route('admin.gateways.update', $gateway) }}">
                            @csrf
                            @method('PUT')

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Display Name</label>
                                    <input type="text" name="name" value="{{ old('name', $gateway->name) }}" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Environment Mode</label>
                                    <select name="is_sandbox" class="form-select form-select-sm">
                                        <option value="1" {{ $gateway->is_sandbox ? 'selected' : '' }}>Sandbox / Test</option>
                                        <option value="0" {{ !$gateway->is_sandbox ? 'selected' : '' }}>Live / Production</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Fixed Fee ($)</label>
                                    <input type="number" step="0.01" name="fee_fixed" value="{{ old('fee_fixed', $gateway->fee_fixed) }}" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Percentage Fee (%)</label>
                                    <input type="number" step="0.01" name="fee_percent" value="{{ old('fee_percent', $gateway->fee_percent) }}" class="form-control form-control-sm">
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="gateway_{{ $gateway->id }}" {{ $gateway->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label small fw-semibold" for="gateway_{{ $gateway->id }}">Gateway Enabled</label>
                                </div>
                                <button type="submit" class="btn btn-sm btn-primary px-3 shadow-sm">
                                    <i class="bi bi-save me-1"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </x-card>
                </div>
            @empty
                <div class="col-12">
                    <x-card>
                        <x-empty-state title="No Gateways Configured" description="Run the payment seeder or configure supported payment gateways." />
                    </x-card>
                </div>
            @endforelse
        </div>
    </div>
</x-admin-layout>
