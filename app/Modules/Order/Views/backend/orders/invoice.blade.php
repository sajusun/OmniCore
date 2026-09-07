<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->order_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            background: #f8fafc;
        }
        .invoice-card {
            max-width: 800px;
            margin: 30px auto;
            background: #fff;
            padding: 40px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        @media print {
            body {
                background: #fff;
            }
            .invoice-card {
                border: none;
                box-shadow: none;
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="text-center my-3 no-print">
            <button onclick="window.print()" class="btn btn-primary me-2"><i class="fa fa-print"></i> Print Invoice</button>
            <button onclick="window.close()" class="btn btn-outline-secondary">Close Window</button>
        </div>

        <div class="invoice-card">
            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
                <div>
                    <h3 class="fw-bold text-primary mb-1">{{ config('app.name', 'OmniCore') }} Store</h3>
                    <p class="text-muted small mb-0">E-Commerce Invoice & Packing Slip</p>
                </div>
                <div class="text-end">
                    <h4 class="fw-bold mb-1">INVOICE</h4>
                    <div class="text-muted small"><strong>Invoice #:</strong> {{ $order->order_number }}</div>
                    <div class="text-muted small"><strong>Date:</strong> {{ $order->created_at->format('M d, Y') }}</div>
                </div>
            </div>

            {{-- Bill To / Ship To --}}
            <div class="row mb-4">
                <div class="col-6">
                    <h6 class="fw-bold text-uppercase small text-muted">Customer & Billed To:</h6>
                    <p class="fw-bold mb-1">{{ $order->user ? $order->user->name : ($order->shipping_address['recipient_name'] ?? 'Customer') }}</p>
                    <p class="text-muted small mb-0">{{ $order->user ? $order->user->email : ($order->shipping_address['email'] ?? '') }}</p>
                    <p class="text-muted small mb-0">{{ $order->shipping_address['phone'] ?? '' }}</p>
                </div>
                <div class="col-6 text-end">
                    <h6 class="fw-bold text-uppercase small text-muted">Ship To Address:</h6>
                    @if($order->shipping_address)
                        <p class="fw-bold mb-1">{{ $order->shipping_address['recipient_name'] ?? '' }}</p>
                        <p class="text-muted small mb-0">{{ $order->shipping_address['address_line_1'] ?? '' }}</p>
                        <p class="text-muted small mb-0">{{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['state'] ?? '' }} {{ $order->shipping_address['postal_code'] ?? '' }}</p>
                        <p class="text-muted small mb-0">{{ $order->shipping_address['country'] ?? '' }}</p>
                    @endif
                </div>
            </div>

            {{-- Items Table --}}
            <table class="table table-bordered mb-4">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Item & Description</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Unit Price</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $idx => $item)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>
                            <span class="fw-bold">{{ $item->product_name }}</span>
                            @if($item->variant_name)
                                <div class="small text-muted">{{ $item->variant_name }}</div>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-end">${{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Totals --}}
            <div class="row justify-content-end mb-4">
                <div class="col-5">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted">Subtotal:</td>
                            <td class="text-end fw-medium">${{ number_format($order->subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Shipping:</td>
                            <td class="text-end fw-medium">${{ number_format($order->shipping_amount, 2) }}</td>
                        </tr>
                        @if($order->discount_amount > 0)
                        <tr class="text-danger">
                            <td>Discount:</td>
                            <td class="text-end">-${{ number_format($order->discount_amount, 2) }}</td>
                        </tr>
                        @endif
                        @if($order->tax_amount > 0)
                        <tr>
                            <td class="text-muted">Tax:</td>
                            <td class="text-end fw-medium">${{ number_format($order->tax_amount, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="border-top fs-5 fw-bold text-dark">
                            <td>Grand Total:</td>
                            <td class="text-end text-success">${{ number_format($order->total_amount, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Payment status note --}}
            <div class="border-top pt-3 text-center text-muted small">
                <p class="mb-1">Payment Method: <strong>{{ strtoupper($order->payment_method ?? 'COD') }}</strong> | Payment Status: <strong>{{ strtoupper($order->payment_status) }}</strong></p>
                <p class="mb-0">Thank you for your business!</p>
            </div>
        </div>
    </div>
</body>
</html>
