@extends('backend.layouts.app')

@section('title', 'Vendor Payouts')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <a href="{{ route('admin.vendors.index') }}" class="text-decoration-none text-muted small mb-2 d-inline-block">
                <i class="fa fa-arrow-left me-1"></i> Back to Stores
            </a>
            <h1 class="h3 fw-bold text-gray-800 mb-0">
                <i class="fa fa-money text-success me-2"></i> Vendor Payout Settlements
            </h1>
        </div>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Transaction ID</th>
                            <th>Store</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Processed Date</th>
                            <th class="pe-4 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payouts as $payout)
                            <tr>
                                <td class="ps-4 fw-bold text-dark"><code>{{ $payout->transaction_id ?? ('#PO-' . $payout->id) }}</code></td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $payout->store->name ?? 'Store #' . $payout->vendor_id }}</div>
                                    <div class="text-muted small">Requester: {{ $payout->requester->name ?? '' }}</div>
                                </td>
                                <td class="fw-bold text-success fs-6">${{ number_format($payout->amount, 2) }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ ucfirst(str_replace('_', ' ', $payout->payout_method)) }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $payout->status->badgeClass() }}">
                                        {{ $payout->status->label() }}
                                    </span>
                                </td>
                                <td class="text-muted small">
                                    {{ $payout->processed_at ? $payout->processed_at->format('M d, Y h:i A') : 'Pending' }}
                                </td>
                                <td class="pe-4 text-end">
                                    @if($payout->status === \App\Modules\Vendor\Enums\PayoutStatus::PENDING)
                                        <form method="POST" action="{{ route('admin.vendors.payouts.process', $payout) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="btn btn-sm btn-success rounded-3 me-1">Approve</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.vendors.payouts.process', $payout) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="failed">
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-3">Reject</button>
                                        </form>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">No payout requests found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($payouts->hasPages())
                <div class="p-3 border-top">
                    {{ $payouts->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
