<x-admin-layout>
    @slot('title')
        Affiliate Commission Logs
    @endslot

    <div class="container-fluid py-4">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Affiliate Commission Logs</h4>
                <p class="text-muted small mb-0">Audit conversions and payout history across partner referral purchases.</p>
            </div>
            <a href="{{ route('admin.affiliate.index') }}" class="btn btn-outline-secondary px-3">
                <i class="bi bi-arrow-left me-1"></i> Back to Affiliates
            </a>
        </div>

        {{-- Status Notification Modal --}}
        <x-modal.status />

        {{-- Commission Logs Table --}}
        <x-card title="Commission Conversions Audit Trail">
            <x-table>
                <thead>
                    <tr>
                        <x-table.th>ID</x-table.th>
                        <x-table.th>Partner</x-table.th>
                        <x-table.th>Order Amount</x-table.th>
                        <x-table.th>Commission Rate</x-table.th>
                        <x-table.th>Commission Earned</x-table.th>
                        <x-table.th>Status</x-table.th>
                        <x-table.th class="text-end">Date</x-table.th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($commissions as $commission)
                        <tr>
                            <x-table.td class="text-muted small">#{{ $commission->id }}</x-table.td>
                            <x-table.td>
                                <div class="fw-bold text-dark">{{ $commission->affiliateAccount?->user?->name ?? 'Unknown Partner' }}</div>
                                <div class="text-muted small">Code: <span class="font-monospace">{{ $commission->affiliateAccount?->referral_code ?? 'N/A' }}</span></div>
                            </x-table.td>
                            <x-table.td class="fw-semibold text-dark">${{ number_format($commission->order_amount, 2) }}</x-table.td>
                            <x-table.td class="text-muted">{{ $commission->commission_rate }}%</x-table.td>
                            <x-table.td>
                                <span class="fw-bold text-success">+${{ number_format($commission->commission_amount, 2) }}</span>
                            </x-table.td>
                            <x-table.td>
                                @if($commission->status === 'paid')
                                    <x-badge color="success">Paid</x-badge>
                                @elseif($commission->status === 'pending')
                                    <x-badge color="warning">Pending</x-badge>
                                @else
                                    <x-badge color="secondary">{{ ucfirst($commission->status) }}</x-badge>
                                @endif
                            </x-table.td>
                            <x-table.td class="text-end text-muted small">{{ $commission->created_at?->format('M d, Y H:i') }}</x-table.td>
                        </tr>
                    @empty
                        <x-empty-state colspan="7" title="No Commission Logs Found" description="No affiliate conversions have occurred yet." />
                    @endforelse
                </tbody>
            </x-table>

            <div class="mt-3">
                {{ $commissions->links() }}
            </div>
        </x-card>
    </div>
</x-admin-layout>
