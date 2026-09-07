<x-admin-layout>
    <x-slot name="title">Orders Management</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Orders Management</h2>
    </x-slot>

    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.875rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item active text-dark fw-medium">Orders</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0">Customer Orders</h4>
        </div>
        <div>
            <a href="{{ route('admin.analytics.ecommerce') }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-2">
                <i class="fa fa-chart-line"></i> Sales Analytics
            </a>
        </div>
    </div>

    {{-- Status Notification Modal --}}
    <x-modal.status />

    <x-card title="Orders Directory ({{ \App\Modules\Order\Models\Order::count() }})">
        <x-datatable id="orders-datatable" :url="route('admin.orders.index')" :order="[[0, 'desc']]" :columns="[
            ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'SL', 'orderable' => false, 'searchable' => false],
            ['data' => 'order_number_display', 'name' => 'order_number', 'title' => 'Order #'],
            ['data' => 'customer', 'name' => 'user.name', 'title' => 'Customer Details'],
            ['data' => 'items_count', 'name' => 'items_count', 'title' => 'Items', 'orderable' => false, 'searchable' => false],
            ['data' => 'total_amount', 'name' => 'total_amount', 'title' => 'Total Paid'],
            ['data' => 'status', 'name' => 'status', 'title' => 'Order Status'],
            ['data' => 'payment_status', 'name' => 'payment_status', 'title' => 'Payment Status'],
            ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false]
        ]" />
    </x-card>
</x-admin-layout>
