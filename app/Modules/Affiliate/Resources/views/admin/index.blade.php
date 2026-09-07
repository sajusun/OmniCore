<x-admin-layout>
    @slot('title')
        Affiliate Network
    @endslot

    <div class="container-fluid py-4">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Affiliate & Partner Network</h4>
                <p class="text-muted small mb-0">Monitor affiliate performance, referrals, custom commissions, and payouts.</p>
            </div>
            <a href="{{ route('admin.affiliate.commissions.index') }}" class="btn btn-primary px-3 shadow-sm">
                <i class="bi bi-clock-history me-1"></i> Commission Logs
            </a>
        </div>

        {{-- Status Notification Modal --}}
        <x-modal.status />

        {{-- Top Metrics --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <x-stat-card 
                    title="Active Partners" 
                    value="{{ number_format($totalAffiliates) }}" 
                    color="primary" 
                    icon="<i class='bi bi-people-fill fs-3'></i>" 
                />
            </div>
            <div class="col-md-4">
                <x-stat-card 
                    title="Total Referral Clicks" 
                    value="{{ number_format($totalClicks) }}" 
                    color="indigo" 
                    icon="<i class='bi bi-cursor-fill fs-3'></i>" 
                />
            </div>
            <div class="col-md-4">
                <x-stat-card 
                    title="Commissions Disbursed" 
                    value="${{ number_format($totalPaidCommissions, 2) }}" 
                    color="emerald" 
                    icon="<i class='bi bi-cash-coin fs-3'></i>" 
                />
            </div>
        </div>

        {{-- Affiliates Card & Table --}}
        <x-card title="Affiliate Partners Directory">
            <x-table>
                <thead>
                    <tr>
                        <x-table.th>Partner</x-table.th>
                        <x-table.th>Referral Code / Slug</x-table.th>
                        <x-table.th>Clicks</x-table.th>
                        <x-table.th>Conversions</x-table.th>
                        <x-table.th>Balance</x-table.th>
                        <x-table.th>Custom Rate</x-table.th>
                        <x-table.th class="text-end">Actions</x-table.th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($affiliates as $affiliate)
                        <tr>
                            <x-table.td>
                                <div class="fw-bold text-dark">{{ $affiliate->user?->name ?? 'N/A' }}</div>
                                <div class="text-muted small">{{ $affiliate->user?->email }}</div>
                            </x-table.td>
                            <x-table.td>
                                <span class="font-monospace bg-light px-2 py-1 border rounded small">{{ $affiliate->referral_code }}</span>
                                @if($affiliate->custom_slug)
                                    <div class="text-primary small mt-1">/ref/{{ $affiliate->custom_slug }}</div>
                                @endif
                            </x-table.td>
                            <x-table.td class="text-muted">{{ number_format($affiliate->total_clicks) }}</x-table.td>
                            <x-table.td class="text-muted">{{ number_format($affiliate->total_conversions) }}</x-table.td>
                            <x-table.td>
                                <span class="fw-bold text-success">${{ number_format($affiliate->balance, 2) }}</span>
                            </x-table.td>
                            <x-table.td>
                                @if($affiliate->custom_commission_rate)
                                    <x-badge color="info">{{ $affiliate->custom_commission_rate }}%</x-badge>
                                @else
                                    <span class="text-muted small">Default (10%)</span>
                                @endif
                            </x-table.td>
                            <x-table.td class="text-end">
                                <form action="{{ route('admin.affiliate.update', $affiliate->id) }}" method="POST" class="d-inline-flex gap-2 align-items-center justify-content-end">
                                    @csrf
                                    @method('PUT')
                                    <input type="number" step="0.5" name="custom_commission_rate" value="{{ $affiliate->custom_commission_rate }}" placeholder="Rate %" class="form-control form-control-sm" style="width: 85px;">
                                    <select name="status" class="form-select form-select-sm" style="width: 105px;">
                                        <option value="active" {{ $affiliate->status === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="suspended" {{ $affiliate->status === 'suspended' ? 'selected' : '' }}>Suspend</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary px-3">Save</button>
                                </form>
                            </x-table.td>
                        </tr>
                    @empty
                        <x-empty-state colspan="7" title="No Affiliate Partners Found" description="There are currently no active or registered affiliates." />
                    @endforelse
                </tbody>
            </x-table>

            <div class="mt-3">
                {{ $affiliates->links() }}
            </div>
        </x-card>
    </div>
</x-admin-layout>
