<x-admin-layout>
    <x-slot name="title">Orders Management</x-slot>

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.875rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item active text-dark fw-medium">Orders</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0">Customer Orders</h4>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
        <div class="card-body p-3">
            <x-datatable id="orders-datatable" :url="route('admin.orders.index')" :order="[[0, 'desc']]" :columns="[
                ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'SL', 'orderable' => false, 'searchable' => false],
                ['data' => 'order_number', 'name' => 'order_number', 'title' => 'Order #'],
                ['data' => 'customer', 'name' => 'user.name', 'title' => 'Customer'],
                ['data' => 'items_count', 'name' => 'items_count', 'title' => 'Items', 'orderable' => false],
                ['data' => 'total_amount', 'name' => 'total_amount', 'title' => 'Total Amount'],
                ['data' => 'status', 'name' => 'status', 'title' => 'Order Status'],
                ['data' => 'payment_status', 'name' => 'payment_status', 'title' => 'Payment Status'],
                ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false]
            ]" />
        </div>
    </div>
</x-admin-layout>
