@extends('backend.app')

@section('title', 'Affiliate Commissions')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <a href="{{ route('admin.affiliates.index') }}" class="text-decoration-none text-muted small mb-2 d-inline-block">
                <i class="fa fa-arrow-left me-1"></i> Back to Partners
            </a>
            <h1 class="h3 fw-bold text-gray-800 mb-0">
                <i class="fa fa-money text-success me-2"></i> Commission Audit Log
            </h1>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.affiliates.commissions') }}" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <select name="status" class="form-select rounded-3">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-3">
                        <i class="fa fa-filter me-1"></i> Filter
                    </button>
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
                            <th class="ps-4">Commission ID</th>
                            <th>Partner</th>
                            <th>Order Amount</th>
                            <th>Rate</th>
                            <th>Commission Earned</th>
                            <th>Status</th>
                            <th class="pe-4">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($commissions as $comm)
                            <tr>
                                <td class="ps-4 fw-bold text-dark">#COM-{{ str_pad((string)$comm->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $comm->affiliate->user->name ?? 'User #' . $comm->affiliate->user_id }}</div>
                                    <div class="text-muted small"><code>{{ $comm->affiliate->referral_code ?? '' }}</code></div>
                                </td>
                                <td>${{ number_format($comm->order_amount, 2) }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $comm->commission_rate }}%</span></td>
                                <td class="fw-bold text-success fs-6">${{ number_format($comm->commission_amount, 2) }}</td>
                                <td>
                                    @if($comm->status === 'paid')
                                        <span class="badge bg-success-subtle text-success">Paid</span>
                                    @elseif($comm->status === 'approved')
                                        <span class="badge bg-primary-subtle text-primary">Approved</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning">{{ ucfirst($comm->status) }}</span>
                                    @endif
                                </td>
                                <td class="pe-4 text-muted small">
                                    {{ $comm->created_at->format('M d, Y h:i A') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">No commission records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($commissions->hasPages())
                <div class="p-3 border-top">
                    {{ $commissions->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
