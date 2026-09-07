<x-admin-layout>
    @slot('title')
        Affiliate Partners
    @endslot

    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row align-items-center mb-4">
            <div class="col">
                <h4 class="fw-bold mb-1">
                    <i class="fa fa-users text-primary me-2"></i> Affiliate & Referral Program
                </h4>
                <p class="text-muted small mb-0">Manage partner referral programs, commission rates, and payouts.</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.affiliates.commissions') }}" class="btn btn-outline-primary">
                    <i class="fa fa-money me-1"></i> Commission Logs
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 bg-white">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary-subtle text-primary p-3 me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fa fa-handshake-o fa-lg"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold">Total Partners</div>
                            <div class="fs-4 fw-bold text-dark">{{ number_format($stats['total_affiliates']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 bg-white">
                    <div class="d-flex align-items-center">
                        <div class="bg-success-subtle text-success p-3 me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fa fa-check-circle fa-lg"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold">Active Partners</div>
                            <div class="fs-4 fw-bold text-success">{{ number_format($stats['active_affiliates']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 bg-white">
                    <div class="d-flex align-items-center">
                        <div class="bg-info-subtle text-info p-3 me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fa fa-dollar fa-lg"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold">Total Paid Out</div>
                            <div class="fs-4 fw-bold text-info">${{ number_format($stats['total_paid_out'], 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 bg-white">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning-subtle text-warning p-3 me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fa fa-hourglass-half fa-lg"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold">Unpaid Balance</div>
                            <div class="fs-4 fw-bold text-warning">${{ number_format($stats['pending_balance'], 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('admin.affiliates.index') }}" class="row g-2 align-items-center">
                    <div class="col-md-6">
                        <input type="text" name="search" class="form-control" placeholder="Search partner name, email, or referral code..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            @foreach(\App\Modules\Affiliate\Enums\AffiliateStatus::cases() as $st)
                                <option value="{{ $st->value }}" {{ request('status') === $st->value ? 'selected' : '' }}>{{ $st->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa fa-filter me-1"></i> Filter
                        </button>
                        @if(request()->hasAny(['search', 'status']))
                            <a href="{{ route('admin.affiliates.index') }}" class="btn btn-outline-secondary">
                                <i class="fa fa-times"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Partner</th>
                                <th>Referral Code</th>
                                <th>Commission Rate</th>
                                <th>Total Earned</th>
                                <th>Current Balance</th>
                                <th>Conversions / Visits</th>
                                <th>Status</th>
                                <th class="pe-4 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($affiliates as $account)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">{{ $account->user->name ?? 'User #' . $account->user_id }}</div>
                                        <div class="text-muted small">{{ $account->user->email ?? '' }}</div>
                                    </td>
                                    <td>
                                        <code>{{ $account->referral_code }}</code>
                                        @if($account->custom_slug)
                                            <div class="text-muted small">/{{ $account->custom_slug }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary">
                                            {{ $account->commission_rate }}{{ $account->commission_type === \App\Modules\Affiliate\Enums\CommissionType::PERCENTAGE ? '%' : '$' }}
                                        </span>
                                    </td>
                                    <td class="fw-bold text-success">${{ number_format($account->total_earnings, 2) }}</td>
                                    <td class="fw-semibold text-dark">${{ number_format($account->current_balance, 2) }}</td>
                                    <td>
                                        <div class="small fw-semibold text-dark">{{ $account->lifetime_conversions }} orders</div>
                                        <div class="text-muted small">{{ $account->lifetime_referrals }} clicks</div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $account->status->badgeClass() }}">
                                            {{ $account->status->label() }}
                                        </span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $account->id }}">
                                            <i class="fa fa-edit me-1"></i> Edit Rates
                                        </button>
                                    </td>
                                </tr>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editModal{{ $account->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold">Affiliate Settings: {{ $account->user->name ?? '' }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form method="POST" action="{{ route('admin.affiliates.update', $account) }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Commission Rate</label>
                                                        <input type="number" step="0.01" name="commission_rate" class="form-control" value="{{ $account->commission_rate }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Commission Type</label>
                                                        <select name="commission_type" class="form-select">
                                                            @foreach(\App\Modules\Affiliate\Enums\CommissionType::cases() as $type)
                                                                <option value="{{ $type->value }}" {{ $account->commission_type === $type ? 'selected' : '' }}>{{ $type->label() }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Account Status</label>
                                                        <select name="status" class="form-select">
                                                            @foreach(\App\Modules\Affiliate\Enums\AffiliateStatus::cases() as $st)
                                                                <option value="{{ $st->value }}" {{ $account->status === $st ? 'selected' : '' }}>{{ $st->label() }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Save Settings</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="fa fa-users fa-3x mb-3 text-secondary opacity-50 d-block"></i>
                                        No affiliate accounts found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($affiliates->hasPages())
                    <div class="p-3 border-top">
                        {{ $affiliates->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
