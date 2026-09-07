<x-admin-layout>
    @slot('title')
        Payment Transactions
    @endslot

    <div class="container-fluid py-4">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1"><i class="bi bi-clock-history me-2 text-primary"></i> Payment Transactions & Gateway Logs</h4>
                <p class="text-muted small mb-0">Audit customer checkout payments, gateway response signatures, refunds, and settlement records.</p>
            </div>
            <a href="{{ route('admin.gateways.index') }}" class="btn btn-outline-secondary px-3">
                <i class="bi bi-gear me-1"></i> Gateway Settings
            </a>
        </div>

        {{-- Status Notification Modal --}}
        <x-modal.status />

        {{-- Stats Row --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <x-stat-card 
                    title="Total Transactions" 
                    value="{{ number_format($stats['total_payments']) }}" 
                    color="primary" 
                    icon="<i class='bi bi-receipt fs-3'></i>" 
                />
            </div>
            <div class="col-md-3">
                <x-stat-card 
                    title="Total Processed Volume" 
                    value="${{ number_format($stats['total_revenue'], 2) }}" 
                    color="emerald" 
                    icon="<i class='bi bi-cash-stack fs-3'></i>" 
                />
            </div>
            <div class="col-md-3">
                <x-stat-card 
                    title="Completed" 
                    value="{{ number_format($stats['completed_count']) }}" 
                    color="emerald" 
                    icon="<i class='bi bi-check2-circle fs-3'></i>" 
                />
            </div>
            <div class="col-md-3">
                <x-stat-card 
                    title="Pending" 
                    value="{{ number_format($stats['pending_count']) }}" 
                    color="warning" 
                    icon="<i class='bi bi-hourglass-split fs-3'></i>" 
                />
            </div>
        </div>

        {{-- Search & Filter Card --}}
        <x-card title="Search & Filter Transactions" class="mb-4">
            <form method="GET" action="{{ route('admin.payments.index') }}" class="row g-3 align-items-center">
                <div class="col-md-4">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Payment UUID, Trx ID, Customer..." class="form-control">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="method" class="form-select">
                        <option value="">All Gateways</option>
                        <option value="stripe" {{ request('method') === 'stripe' ? 'selected' : '' }}>Stripe</option>
                        <option value="paypal" {{ request('method') === 'paypal' ? 'selected' : '' }}>PayPal</option>
                        <option value="wallet" {{ request('method') === 'wallet' ? 'selected' : '' }}>In-App Wallet</option>
                        <option value="sslcommerz" {{ request('method') === 'sslcommerz' ? 'selected' : '' }}>SSLCommerz</option>
                        <option value="bkash" {{ request('method') === 'bkash' ? 'selected' : '' }}>bKash</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-3 w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-light border px-3">Reset</a>
                </div>
            </form>
        </x-card>

        {{-- Transactions Table --}}
        <x-card title="Payment Records">
            <x-table>
                <thead>
                    <tr>
                        <x-table.th>Payment ID</x-table.th>
                        <x-table.th>Customer</x-table.th>
                        <x-table.th>Gateway</x-table.th>
                        <x-table.th>Amount</x-table.th>
                        <x-table.th>Fee</x-table.th>
                        <x-table.th>Status</x-table.th>
                        <x-table.th>Date</x-table.th>
                        <x-table.th class="text-end">Action</x-table.th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <x-table.td>
                                <a href="{{ route('admin.payments.show', $payment) }}" class="fw-bold text-primary font-monospace text-decoration-none small">
                                    {{ Str::limit($payment->payment_id, 12, '...') }}
                                </a>
                                @if($payment->gateway_transaction_id)
                                    <div class="text-muted small font-monospace">Trx: {{ Str::limit($payment->gateway_transaction_id, 14, '...') }}</div>
                                @endif
                            </x-table.td>
                            <x-table.td>
                                <div class="fw-bold text-dark">{{ $payment->user?->name ?? 'Guest / System' }}</div>
                                <div class="text-muted small">{{ $payment->user?->email }}</div>
                            </x-table.td>
                            <x-table.td>
                                <span class="badge bg-light text-dark border text-uppercase">{{ $payment->gateway ?? 'N/A' }}</span>
                            </x-table.td>
                            <x-table.td class="fw-bold text-dark">
                                ${{ number_format($payment->amount, 2) }} {{ strtoupper($payment->currency ?? 'USD') }}
                            </x-table.td>
                            <x-table.td class="text-muted small">
                                ${{ number_format($payment->fee ?? 0, 2) }}
                            </x-table.td>
                            <x-table.td>
                                @php
                                    $st = $payment->status?->value ?? $payment->status;
                                    $color = match($st) {
                                        'completed' => 'success',
                                        'pending' => 'warning',
                                        'failed' => 'danger',
                                        'refunded' => 'secondary',
                                        default => 'primary'
                                    };
                                @endphp
                                <x-badge :color="$color">{{ ucfirst($st) }}</x-badge>
                            </x-table.td>
                            <x-table.td class="text-muted small">
                                {{ $payment->created_at->format('M d, Y H:i') }}
                            </x-table.td>
                            <x-table.td class="text-end">
                                <a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-sm btn-outline-primary px-2.5">
                                    <i class="bi bi-eye me-1"></i> Details
                                </a>
                            </x-table.td>
                        </tr>
                    @empty
                        <x-empty-state colspan="8" title="No Payments Found" description="No transactions match your search filters." />
                    @endforelse
                </tbody>
            </x-table>

            <div class="mt-3">
                {{ $payments->links() }}
            </div>
        </x-card>
    </div>
</x-admin-layout>
