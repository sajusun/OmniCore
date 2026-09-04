<x-admin-layout>
    <x-slot name="title">Order Details #{{ $order->order_number }}</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Order Details</h2>
    </x-slot>

    <div class="container-fluid py-2 px-0">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
            <div>
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb mb-0" style="font-size: 0.875rem;">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}" class="text-decoration-none text-muted">Orders</a></li>
                        <li class="breadcrumb-item active text-dark fw-medium">#{{ $order->order_number }}</li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center gap-2">
                    <h4 class="fw-bold text-dark mb-0">Order #{{ $order->order_number }}</h4>
                    <span class="badge bg-primary text-uppercase">{{ $order->status }}</span>
                    <span class="badge {{ $order->payment_status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }} text-capitalize">{{ $order->payment_status }}</span>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="btn btn-outline-secondary">
                    <i class="fa fa-print me-1"></i> Print Invoice
                </a>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-dark">
                    <i class="fa fa-arrow-left me-1"></i> Back to Orders
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4">
            {{-- Left Column: Items & Timeline --}}
            <div class="col-lg-8">
                {{-- Order Items Table --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold text-dark"><i class="fa fa-boxes-packing me-2 text-primary"></i> Ordered Items ({{ $order->items->count() }})</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Product Details</th>
                                        <th class="text-center">Price</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-end pe-4">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="{{ $item->product ? ($item->product->thumbnail_url ?? asset('default/product.png')) : asset('default/product.png') }}" class="rounded border" style="width: 44px; height: 44px; object-fit: cover;" onError="this.src='https://placehold.co/100x100?text=Product';">
                                                <div>
                                                    <div class="fw-bold">{{ $item->product_name }}</div>
                                                    @if($item->variant_name)
                                                        <span class="badge bg-light text-dark border small">{{ $item->variant_name }}</span>
                                                    @endif
                                                    <small class="text-muted d-block">SKU: {{ $item->sku ?? 'N/A' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center fw-medium">${{ number_format($item->unit_price, 2) }}</td>
                                        <td class="text-center fw-bold">{{ $item->quantity }}</td>
                                        <td class="text-end pe-4 fw-bold text-success">${{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Calculation Breakdown --}}
                        <div class="p-4 border-top bg-light">
                            <div class="row justify-content-end">
                                <div class="col-md-5">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Items Subtotal:</span>
                                        <span class="fw-medium">${{ number_format($order->subtotal, 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Shipping Fee:</span>
                                        <span class="fw-medium">${{ number_format($order->shipping_amount, 2) }}</span>
                                    </div>
                                    @if($order->discount_amount > 0)
                                    <div class="d-flex justify-content-between mb-2 text-danger">
                                        <span>Discount ({{ $order->coupon_code ?? 'Coupon' }}):</span>
                                        <span>-${{ number_format($order->discount_amount, 2) }}</span>
                                    </div>
                                    @endif
                                    @if($order->tax_amount > 0)
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Tax / VAT:</span>
                                        <span class="fw-medium">${{ number_format($order->tax_amount, 2) }}</span>
                                    </div>
                                    @endif
                                    <hr>
                                    <div class="d-flex justify-content-between fs-5 fw-bold text-dark">
                                        <span>Grand Total:</span>
                                        <span class="text-success">${{ number_format($order->total_amount, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Order History / Timeline --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold text-dark"><i class="fa fa-timeline me-2 text-info"></i> Order History & Status Timeline</h5>
                    </div>
                    <div class="card-body p-4">
                        @if($order->histories && $order->histories->isNotEmpty())
                            <ul class="list-unstyled mb-0 position-relative">
                                @foreach($order->histories as $history)
                                <li class="mb-3 d-flex align-items-start gap-3">
                                    <div class="badge bg-primary rounded-circle p-2 mt-1">
                                        <i class="fa fa-check text-white" style="font-size: 0.75rem;"></i>
                                    </div>
                                    <div class="flex-grow-1 border-bottom pb-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fw-bold text-dark text-capitalize">{{ ucfirst(str_replace('_', ' ', $history->status)) }}</span>
                                            <small class="text-muted">{{ $history->created_at->format('M d, Y - h:i A') }}</small>
                                        </div>
                                        @if($history->notes)
                                            <p class="small text-muted mb-0 mt-1">{{ $history->notes }}</p>
                                        @endif
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted mb-0">Order placed on {{ $order->created_at->format('F d, Y - h:i A') }}.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right Column: Status Updater, Customer & Shipping --}}
            <div class="col-lg-4">
                {{-- Status Updater --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold text-dark"><i class="fa fa-arrows-rotate me-2 text-primary"></i> Update Order Status</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold">Order Status</label>
                                <select name="status" class="form-select" required>
                                    @foreach(['pending' => 'Pending', 'confirmed' => 'Confirmed', 'processing' => 'Processing', 'shipped' => 'Shipped', 'out_for_delivery' => 'Out for Delivery', 'delivered' => 'Delivered', 'cancelled' => 'Cancelled', 'refunded' => 'Refunded'] as $val => $lbl)
                                        <option value="{{ $val }}" {{ $order->status === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Courier / Tracking Number</label>
                                <input type="text" name="tracking_number" class="form-control" value="{{ $order->tracking_number }}" placeholder="e.g. TRK-8923489">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Status Update Note</label>
                                <textarea name="notes" rows="2" class="form-control" placeholder="Optional notes for history timeline..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                                <i class="fa fa-save me-1"></i> Update Status
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Customer Details --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold text-dark"><i class="fa fa-user me-2 text-info"></i> Customer Information</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="avatar avatar-md bg-light-primary text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                <i class="fa fa-user fs-5"></i>
                            </div>
                            <div>
                                <div class="fw-bold">{{ $order->user ? $order->user->name : ($order->shipping_address['recipient_name'] ?? 'Guest Customer') }}</div>
                                <small class="text-muted">{{ $order->user ? $order->user->email : ($order->shipping_address['email'] ?? 'N/A') }}</small>
                            </div>
                        </div>

                        <ul class="list-group list-group-flush small">
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span>Phone:</span>
                                <span class="fw-medium">{{ $order->shipping_address['phone'] ?? ($order->user->phone ?? 'N/A') }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span>Payment Method:</span>
                                <span class="fw-medium text-uppercase">{{ $order->payment_method ?? 'COD' }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Delivery Address --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold text-dark"><i class="fa fa-location-dot me-2 text-danger"></i> Shipping Address</h5>
                    </div>
                    <div class="card-body p-4">
                        @if($order->shipping_address)
                            <p class="mb-1 fw-bold">{{ $order->shipping_address['recipient_name'] ?? '' }}</p>
                            <p class="mb-1 text-muted">{{ $order->shipping_address['address_line_1'] ?? '' }}</p>
                            @if(!empty($order->shipping_address['address_line_2']))
                                <p class="mb-1 text-muted">{{ $order->shipping_address['address_line_2'] }}</p>
                            @endif
                            <p class="mb-1 text-muted">{{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['state'] ?? '' }} - {{ $order->shipping_address['postal_code'] ?? '' }}</p>
                            <p class="mb-0 text-muted">{{ $order->shipping_address['country'] ?? '' }}</p>
                        @else
                            <p class="text-muted mb-0">No shipping address recorded.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
