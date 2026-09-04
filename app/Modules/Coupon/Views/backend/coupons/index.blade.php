<x-admin-layout>
    <x-slot name="title">Discount Coupons</x-slot>

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.875rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item active text-dark fw-medium">Coupons</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0">Discount Coupons</h4>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
        <div class="card-body p-3">
            <x-datatable id="coupons-datatable" :url="route('admin.coupons.index')" :columns="[
                ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'SL', 'orderable' => false, 'searchable' => false],
                ['data' => 'code', 'name' => 'code', 'title' => 'Coupon Code'],
                ['data' => 'discount', 'name' => 'value', 'title' => 'Discount'],
                ['data' => 'min_spend', 'name' => 'min_order_amount', 'title' => 'Min Spend'],
                ['data' => 'times_used', 'name' => 'times_used', 'title' => 'Times Used'],
                ['data' => 'status', 'name' => 'is_active', 'title' => 'Status']
            ]" />
        </div>
    </div>
</x-admin-layout>
