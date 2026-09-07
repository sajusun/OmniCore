<x-admin-layout>
    @slot('title')
        User Wallets
    @endslot

    <div class="container-fluid py-4">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1"><i class="bi bi-wallet2 me-2 text-primary"></i> User Wallets & Ledger Balances</h4>
                <p class="text-muted small mb-0">Monitor stored balance in circulation, manual admin balance adjustments, and freeze controls.</p>
            </div>
            <a href="{{ route('admin.wallets.transactions') }}" class="btn btn-primary px-3 shadow-sm">
                <i class="bi bi-journal-text me-1"></i> Ledger Audit Trail
            </a>
        </div>

        {{-- Status Notification Modal --}}
        <x-modal.status />

        {{-- Stats Row --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <x-stat-card 
                    title="Total User Wallets" 
                    value="{{ number_format($stats['total_wallets']) }}" 
                    color="primary" 
                    icon="<i class='bi bi-person-vcard fs-3'></i>" 
                />
            </div>
            <div class="col-md-4">
                <x-stat-card 
                    title="Total Currency in Circulation" 
                    value="${{ number_format($stats['total_circulation'], 2) }}" 
                    color="emerald" 
                    icon="<i class='bi bi-cash-coin fs-3'></i>" 
                />
            </div>
            <div class="col-md-4">
                <x-stat-card 
                    title="Frozen / Locked Wallets" 
                    value="{{ number_format($stats['frozen_wallets']) }}" 
                    color="danger" 
                    icon="<i class='bi bi-lock-fill fs-3'></i>" 
                />
            </div>
        </div>

        {{-- Search Card --}}
        <x-card title="Search Wallets" class="mb-4">
            <form method="GET" action="{{ route('admin.wallets.index') }}" class="row g-3 align-items-center">
                <div class="col-md-9">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search customer name or email..." class="form-control">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4 w-100"><i class="bi bi-search me-1"></i> Search</button>
                    <a href="{{ route('admin.wallets.index') }}" class="btn btn-light border px-3">Reset</a>
                </div>
            </form>
        </x-card>

        {{-- Wallets Table --}}
        <x-card title="User Wallets Directory">
            <x-table>
                <thead>
                    <tr>
                        <x-table.th>User</x-table.th>
                        <x-table.th>Current Balance</x-table.th>
                        <x-table.th>Status</x-table.th>
                        <x-table.th>Created Date</x-table.th>
                        <x-table.th class="text-end">Balance Adjustment & Controls</x-table.th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($wallets as $wallet)
                        <tr>
                            <x-table.td>
                                <div class="fw-bold text-dark">{{ $wallet->user?->name ?? 'N/A' }}</div>
                                <div class="text-muted small">{{ $wallet->user?->email }}</div>
                            </x-table.td>
                            <x-table.td>
                                <span class="fw-bold text-success fs-6">${{ number_format($wallet->balance, 2) }} {{ strtoupper($wallet->currency ?? 'USD') }}</span>
                            </x-table.td>
                            <x-table.td>
                                @if($wallet->is_active ?? true)
                                    <x-badge color="success">Active</x-badge>
                                @else
                                    <x-badge color="danger">Frozen</x-badge>
                                @endif
                            </x-table.td>
                            <x-table.td class="text-muted small">
                                {{ $wallet->created_at->format('M d, Y') }}
                            </x-table.td>
                            <x-table.td class="text-end">
                                <div class="d-inline-flex gap-2 align-items-center justify-content-end">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#adjustModal_{{ $wallet->id }}">
                                        <i class="bi bi-sliders me-1"></i> Adjust
                                    </button>
                                    <form method="POST" action="{{ route('admin.wallets.toggle-freeze', $wallet) }}">
                                        @csrf
                                        @if($wallet->is_active ?? true)
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Freeze Wallet">
                                                <i class="bi bi-lock"></i>
                                            </button>
                                        @else
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Unfreeze Wallet">
                                                <i class="bi bi-unlock"></i>
                                            </button>
                                        @endif
                                    </form>
                                </div>

                                {{-- Balance Adjustment Modal --}}
                                <div class="modal fade text-start" id="adjustModal_{{ $wallet->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <form method="POST" action="{{ route('admin.wallets.adjust-balance', $wallet) }}">
                                                @csrf
                                                <div class="modal-header border-bottom">
                                                    <h5 class="modal-title fw-bold">Adjust Balance: {{ $wallet->user?->name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Adjustment Type</label>
                                                        <select name="type" class="form-select" required>
                                                            <option value="credit">Credit / Add Funds (+)</option>
                                                            <option value="debit">Debit / Deduct Funds (-)</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Amount ($)</label>
                                                        <input type="number" step="0.01" min="0.01" name="amount" class="form-control" required placeholder="0.00">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Reason / Audit Description</label>
                                                        <input type="text" name="description" class="form-control" required placeholder="e.g. Administrative refund, manual bonus">
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Confirm Adjustment</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </x-table.td>
                        </tr>
                    @empty
                        <x-empty-state colspan="5" title="No Wallets Found" description="No user wallets match your query." />
                    @endforelse
                </tbody>
            </x-table>

            <div class="mt-3">
                {{ $wallets->links() }}
            </div>
        </x-card>
    </div>
</x-admin-layout>
