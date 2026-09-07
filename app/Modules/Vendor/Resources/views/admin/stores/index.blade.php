<x-admin-layout>
    @slot('title')
        Vendor Stores Management
    @endslot

    <div class="container-fluid py-4">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Vendor Stores & Merchant Network</h4>
                <p class="text-muted small mb-0">Manage registered seller shops, platform commission rates, verification status, and merchant balances.</p>
            </div>
            <a href="{{ route('admin.vendor.payouts.index') }}" class="btn btn-primary px-3 shadow-sm">
                <i class="bi bi-wallet2 me-1"></i> Payout Requests
            </a>
        </div>

        {{-- Status Notification Modal --}}
        <x-modal.status />

        {{-- Top Metrics --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <x-stat-card 
                    title="Total Registered Stores" 
                    value="{{ number_format($totalStores) }}" 
                    color="primary" 
                    icon="<i class='bi bi-shop fs-3'></i>" 
                />
            </div>
            <div class="col-md-4">
                <x-stat-card 
                    title="Verified Merchants" 
                    value="{{ number_format($verifiedStores) }}" 
                    color="emerald" 
                    icon="<i class='bi bi-patch-check-fill fs-3'></i>" 
                />
            </div>
            <div class="col-md-4">
                <x-stat-card 
                    title="Total Vendor Sales Volume" 
                    value="${{ number_format($totalRevenue, 2) }}" 
                    color="indigo" 
                    icon="<i class='bi bi-graph-up-arrow fs-3'></i>" 
                />
            </div>
        </div>

        {{-- Search & Filter Card --}}
        <x-card title="Search & Filter Stores" class="mb-4">
            <form method="GET" action="{{ route('admin.vendor.stores.index') }}" class="row g-3 align-items-center">
                <div class="col-md-5">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search store name, email, owner..." class="form-control">
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Review</option>
                        <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-funnel me-1"></i> Filter</button>
                    <a href="{{ route('admin.vendor.stores.index') }}" class="btn btn-light border px-3">Reset</a>
                </div>
            </form>
        </x-card>

        {{-- Stores Table --}}
        <x-card title="Merchant Stores Directory">
            <x-table>
                <thead>
                    <tr>
                        <x-table.th>Store Name</x-table.th>
                        <x-table.th>Owner</x-table.th>
                        <x-table.th>Balance</x-table.th>
                        <x-table.th>Commission %</x-table.th>
                        <x-table.th>Status</x-table.th>
                        <x-table.th>Verified</x-table.th>
                        <x-table.th class="text-end">Quick Actions</x-table.th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stores as $store)
                        <tr>
                            <x-table.td>
                                <div class="fw-bold text-dark">{{ $store->name }}</div>
                                <div class="text-muted small font-monospace">/store/{{ $store->slug }}</div>
                            </x-table.td>
                            <x-table.td>
                                <div class="text-dark">{{ $store->user?->name ?? 'N/A' }}</div>
                                <div class="text-muted small">{{ $store->user?->email }}</div>
                            </x-table.td>
                            <x-table.td>
                                <span class="fw-bold text-success">${{ number_format($store->balance, 2) }}</span>
                            </x-table.td>
                            <x-table.td class="text-muted">
                                {{ $store->commission_rate }}%
                            </x-table.td>
                            <x-table.td>
                                @if($store->status === 'active')
                                    <x-badge color="success">Active</x-badge>
                                @elseif($store->status === 'pending')
                                    <x-badge color="warning">Pending</x-badge>
                                @else
                                    <x-badge color="danger">Suspended</x-badge>
                                @endif
                            </x-table.td>
                            <x-table.td>
                                @if($store->is_verified)
                                    <span class="text-success small fw-semibold"><i class="bi bi-patch-check-fill me-1"></i> Verified</span>
                                @else
                                    <span class="text-muted small">Unverified</span>
                                @endif
                            </x-table.td>
                            <x-table.td class="text-end">
                                <form action="{{ route('admin.vendor.stores.update', $store->id) }}" method="POST" class="d-inline-flex align-items-center gap-2 justify-content-end">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" class="form-select form-select-sm" style="width: 105px;">
                                        <option value="active" {{ $store->status === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="suspended" {{ $store->status === 'suspended' ? 'selected' : '' }}>Suspend</option>
                                        <option value="pending" {{ $store->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    </select>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" name="is_verified" value="1" id="verify_{{ $store->id }}" {{ $store->is_verified ? 'checked' : '' }}>
                                        <label class="form-check-label small text-muted" for="verify_{{ $store->id }}">Verify</label>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-primary px-3">Save</button>
                                </form>
                            </x-table.td>
                        </tr>
                    @empty
                        <x-empty-state colspan="7" title="No Vendor Stores Found" description="No merchant stores match your search filters." />
                    @endforelse
                </tbody>
            </x-table>

            <div class="mt-3">
                {{ $stores->links() }}
            </div>
        </x-card>
    </div>
</x-admin-layout>
