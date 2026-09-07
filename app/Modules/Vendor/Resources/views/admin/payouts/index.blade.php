<x-admin-layout>
    @slot('title')
        Vendor Payout Requests
    @endslot

    <div class="container-fluid py-4">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Vendor Payout & Disbursement Requests</h4>
                <p class="text-muted small mb-0">Approve or reject vendor withdrawal requests and review disbursement records.</p>
            </div>
            <a href="{{ route('admin.vendor.stores.index') }}" class="btn btn-outline-secondary px-3">
                <i class="bi bi-arrow-left me-1"></i> Back to Stores
            </a>
        </div>

        {{-- Status Notification Modal --}}
        <x-modal.status />

        {{-- Top Metrics --}}
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <x-stat-card 
                    title="Pending Payouts" 
                    value="${{ number_format($pendingAmount, 2) }}" 
                    color="warning" 
                    icon="<i class='bi bi-hourglass-split fs-3'></i>" 
                />
            </div>
            <div class="col-md-6">
                <x-stat-card 
                    title="Completed Disbursements" 
                    value="${{ number_format($completedAmount, 2) }}" 
                    color="emerald" 
                    icon="<i class='bi bi-check2-circle fs-3'></i>" 
                />
            </div>
        </div>

        {{-- Payouts Table Card --}}
        <x-card title="Disbursement Audit Trail">
            <x-table>
                <thead>
                    <tr>
                        <x-table.th>ID</x-table.th>
                        <x-table.th>Store & Owner</x-table.th>
                        <x-table.th>Amount</x-table.th>
                        <x-table.th>Method</x-table.th>
                        <x-table.th>Status</x-table.th>
                        <x-table.th>Requested Date</x-table.th>
                        <x-table.th class="text-end">Actions</x-table.th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payouts as $payout)
                        <tr>
                            <x-table.td class="text-muted small">#{{ $payout->id }}</x-table.td>
                            <x-table.td>
                                <div class="fw-bold text-dark">{{ $payout->vendorStore?->name ?? 'N/A' }}</div>
                                <div class="text-muted small">{{ $payout->vendorStore?->user?->email }}</div>
                            </x-table.td>
                            <x-table.td class="fw-bold text-dark fs-6">
                                ${{ number_format($payout->amount, 2) }}
                            </x-table.td>
                            <x-table.td>
                                <span class="badge bg-light text-dark border uppercase">{{ $payout->payout_method }}</span>
                            </x-table.td>
                            <x-table.td>
                                @if($payout->status === 'completed')
                                    <x-badge color="success">Completed</x-badge>
                                @elseif($payout->status === 'pending')
                                    <x-badge color="warning">Pending Approval</x-badge>
                                @else
                                    <x-badge color="danger">Rejected</x-badge>
                                @endif
                            </x-table.td>
                            <x-table.td class="text-muted small">{{ $payout->created_at->format('M d, Y H:i') }}</x-table.td>
                            <x-table.td class="text-end">
                                @if($payout->status === 'pending')
                                    <div class="d-inline-flex gap-2">
                                        <form action="{{ route('admin.vendor.payouts.approve', $payout->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success px-3">
                                                <i class="bi bi-check-lg me-1"></i> Approve
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.vendor.payouts.reject', $payout->id) }}" method="POST" onsubmit="return confirm('Reject this payout and return funds?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger px-3">
                                                <i class="bi bi-x-lg me-1"></i> Reject
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-muted small">Processed</span>
                                @endif
                            </x-table.td>
                        </tr>
                    @empty
                        <x-empty-state colspan="7" title="No Payout Requests Found" description="There are no pending or historic vendor disbursement requests." />
                    @endforelse
                </tbody>
            </x-table>

            <div class="mt-3">
                {{ $payouts->links() }}
            </div>
        </x-card>
    </div>
</x-admin-layout>
