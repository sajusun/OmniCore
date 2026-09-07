@extends('backend.app')

@section('title', 'Multi-Vendor Stores')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <h1 class="h3 fw-bold text-gray-800 mb-1">
                <i class="fa fa-shopping-bag text-primary me-2"></i> Multi-Vendor Stores
            </h1>
            <p class="text-muted mb-0">Oversee registered seller stores, commission commissions, and payout requests.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.vendors.payouts') }}" class="btn btn-outline-success rounded-3">
                <i class="fa fa-money me-1"></i> Payout Settlements
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary-subtle text-primary p-3 me-3">
                        <i class="fa fa-building fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Total Stores</div>
                        <div class="fs-4 fw-bold text-dark">{{ number_format($stats['total_stores']) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-success-subtle text-success p-3 me-3">
                        <i class="fa fa-check-circle fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Active Stores</div>
                        <div class="fs-4 fw-bold text-success">{{ number_format($stats['active_stores']) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-info-subtle text-info p-3 me-3">
                        <i class="fa fa-line-chart fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Total Gross Volume</div>
                        <div class="fs-4 fw-bold text-info">${{ number_format($stats['total_volume'], 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-warning-subtle text-warning p-3 me-3">
                        <i class="fa fa-credit-card fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Completed Payouts</div>
                        <div class="fs-4 fw-bold text-warning">${{ number_format($stats['total_payouts'], 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.vendors.index') }}" class="row g-2 align-items-center">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control rounded-3" placeholder="Search store name, email, owner..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select rounded-3">
                        <option value="">All Statuses</option>
                        @foreach(\App\Modules\Vendor\Enums\VendorStatus::cases() as $st)
                            <option value="{{ $st->value }}" {{ request('status') === $st->value ? 'selected' : '' }}>{{ $st->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-3">
                        <i class="fa fa-filter me-1"></i> Filter
                    </button>
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('admin.vendors.index') }}" class="btn btn-outline-secondary rounded-3">
                            <i class="fa fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Store</th>
                            <th>Owner</th>
                            <th>Platform Fee</th>
                            <th>Gross Sales</th>
                            <th>Withdrawable Balance</th>
                            <th>Status</th>
                            <th class="pe-4 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stores as $store)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        @if($store->logo_url)
                                            <img src="{{ $store->logo_url }}" class="rounded-circle border" style="width: 38px; height: 38px; object-fit: cover;">
                                        @else
                                            <div class="avatar-sm rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px;">
                                                {{ strtoupper(substr($store->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-bold text-dark">
                                                {{ $store->name }}
                                                @if($store->is_featured)
                                                    <span class="badge bg-warning text-dark ms-1"><i class="fa fa-star"></i> Featured</span>
                                                @endif
                                            </div>
                                            <div class="text-muted small"><code>/store/{{ $store->slug }}</code></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $store->owner->name ?? 'User #' . $store->user_id }}</div>
                                    <div class="text-muted small">{{ $store->owner->email ?? '' }}</div>
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $store->commission_rate }}%</span></td>
                                <td class="fw-bold text-primary">${{ number_format($store->total_sales, 2) }}</td>
                                <td class="fw-bold text-success">${{ number_format($store->balance, 2) }}</td>
                                <td>
                                    <span class="badge {{ $store->status->badgeClass() }}">
                                        {{ $store->status->label() }}
                                    </span>
                                </td>
                                <td class="pe-4 text-end">
                                    <button class="btn btn-sm btn-outline-primary rounded-3" data-bs-toggle="modal" data-bs-target="#editModal{{ $store->id }}">
                                        <i class="fa fa-cog me-1"></i> Manage
                                    </button>
                                </td>
                            </tr>

                            <!-- Edit Store Modal -->
                            <div class="modal fade" id="editModal{{ $store->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content rounded-4 border-0">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Manage Store: {{ $store->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form method="POST" action="{{ route('admin.vendors.update', $store) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Platform Commission Rate (%)</label>
                                                    <input type="number" step="0.01" name="commission_rate" class="form-control rounded-3" value="{{ $store->commission_rate }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Store Status</label>
                                                    <select name="status" class="form-select rounded-3">
                                                        @foreach(\App\Modules\Vendor\Enums\VendorStatus::cases() as $st)
                                                            <option value="{{ $st->value }}" {{ $store->status === $st ? 'selected' : '' }}>{{ $st->label() }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured_check_{{ $store->id }}" {{ $store->is_featured ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-semibold" for="is_featured_check_{{ $store->id }}">Featured Partner Store</label>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary rounded-3">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">No vendor stores registered.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($stores->hasPages())
                <div class="p-3 border-top">
                    {{ $stores->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
