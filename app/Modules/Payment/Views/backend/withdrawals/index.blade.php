<x-admin-layout>
    @slot('title')
        Withdrawal Requests
    @endslot

    <div class="container-fluid py-4">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1"><i class="bi bi-bank me-2 text-primary"></i> User Withdrawal Requests</h4>
                <p class="text-muted small mb-0">Review pending user wallet cashout requests, approve bank disbursements, or reject with reason.</p>
            </div>
            <a href="{{ route('admin.wallets.index') }}" class="btn btn-outline-secondary px-3">
                <i class="bi bi-wallet2 me-1"></i> User Wallets
            </a>
        </div>

        {{-- Status Notification Modal --}}
        <x-modal.status />

        {{-- Stats Row --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <x-stat-card 
                    title="Pending Requests" 
                    value="{{ number_format($stats['pending_count']) }}" 
                    color="warning" 
                    icon="<i class='bi bi-hourglass-split fs-3'></i>" 
                />
            </div>
            <div class="col-md-3">
                <x-stat-card 
                    title="Pending Amount" 
                    value="${{ number_format($stats['pending_amount'], 2) }}" 
                    color="warning" 
                    icon="<i class='bi bi-cash-stack fs-3'></i>" 
                />
            </div>
            <div class="col-md-3">
                <x-stat-card 
                    title="Disbursed Requests" 
                    value="{{ number_format($stats['approved_count']) }}" 
                    color="emerald" 
                    icon="<i class='bi bi-check2-circle fs-3'></i>" 
                />
            </div>
            <div class="col-md-3">
                <x-stat-card 
                    title="Total Disbursed Volume" 
                    value="${{ number_format($stats['approved_amount'], 2) }}" 
                    color="emerald" 
                    icon="<i class='bi bi-wallet fs-3'></i>" 
                />
            </div>
        </div>

        {{-- Filter Card --}}
        <x-card title="Search & Filter Withdrawals" class="mb-4">
            <form method="GET" action="{{ route('admin.withdrawals.index') }}" class="row g-3 align-items-center">
                <div class="col-md-6">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search customer name or email..." class="form-control">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4 w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
                    <a href="{{ route('admin.withdrawals.index') }}" class="btn btn-light border px-3">Reset</a>
                </div>
            </form>
        </x-card>

        {{-- Withdrawals Table --}}
        <x-card title="Withdrawals Directory">
            <x-table>
                <thead>
                    <tr>
                        <x-table.th>Request ID / UUID</x-table.th>
                        <x-table.th>Customer</x-table.th>
                        <x-table.th>Amount</x-table.th>
                        <x-table.th>Disbursement Method</x-table.th>
                        <x-table.th>Status</x-table.th>
                        <x-table.th>Date</x-table.th>
                        <x-table.th class="text-end">Actions</x-table.th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($withdrawals as $withdrawal)
                        <tr>
                            <x-table.td class="font-monospace small">
                                #{{ Str::limit($withdrawal->uuid ?? $withdrawal->id, 10, '...') }}
                            </x-table.td>
                            <x-table.td>
                                <div class="fw-bold text-dark">{{ $withdrawal->user?->name ?? 'N/A' }}</div>
                                <div class="text-muted small">{{ $withdrawal->user?->email }}</div>
                            </x-table.td>
                            <x-table.td>
                                <span class="fw-bold text-dark fs-6">${{ number_format($withdrawal->amount, 2) }}</span>
                                @if($withdrawal->fee > 0)
                                    <div class="text-muted small">Fee: ${{ number_format($withdrawal->fee, 2) }}</div>
                                @endif
                            </x-table.td>
                            <x-table.td>
                                <span class="badge bg-light text-dark border uppercase">{{ $withdrawal->method ?? 'Bank' }}</span>
                            </x-table.td>
                            <x-table.td>
                                @php
                                    $color = match($withdrawal->status) {
                                        'approved' => 'success',
                                        'pending' => 'warning',
                                        'rejected' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <x-badge :color="$color">{{ ucfirst($withdrawal->status) }}</x-badge>
                            </x-table.td>
                            <x-table.td class="text-muted small">
                                {{ $withdrawal->created_at->format('M d, Y H:i') }}
                            </x-table.td>
                            <x-table.td class="text-end">
                                @if($withdrawal->status === 'pending')
                                    <div class="d-inline-flex gap-2 justify-content-end">
                                        <form method="POST" action="{{ route('admin.withdrawals.approve', $withdrawal) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success px-3">
                                                <i class="bi bi-check-lg me-1"></i> Approve
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-danger px-3" data-bs-toggle="modal" data-bs-target="#rejectModal_{{ $withdrawal->id }}">
                                            <i class="bi bi-x-lg me-1"></i> Reject
                                        </button>
                                    </div>

                                    {{-- Rejection Modal --}}
                                    <div class="modal fade text-start" id="rejectModal_{{ $withdrawal->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow">
                                                <form method="POST" action="{{ route('admin.withdrawals.reject', $withdrawal) }}">
                                                    @csrf
                                                    <div class="modal-header border-bottom">
                                                        <h5 class="modal-title fw-bold">Reject Withdrawal #{{ $withdrawal->id }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Rejection Reason <span class="text-danger">*</span></label>
                                                            <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="e.g. Invalid bank details, KYC verification required..."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-top">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted small">Processed</span>
                                @endif
                            </x-table.td>
                        </tr>
                    @empty
                        <x-empty-state colspan="7" title="No Withdrawal Requests" description="No user cashout requests found." />
                    @endforelse
                </tbody>
            </x-table>

            <div class="mt-3">
                {{ $withdrawals->links() }}
            </div>
        </x-card>
    </div>
</x-admin-layout>
