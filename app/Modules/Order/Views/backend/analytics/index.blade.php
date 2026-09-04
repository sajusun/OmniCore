<x-admin-layout>
    <x-slot name="title">E-Commerce Sales Analytics</x-slot>

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.875rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item active text-dark fw-medium">Analytics</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0">E-Commerce Sales & Revenue Analytics</h4>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3" style="border-radius: 0; border-left: 4px solid #10b981 !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-medium">TOTAL REVENUE</span>
                        <h3 class="fw-bold text-dark mt-1 mb-0">${{ number_format($analytics['overview']['total_revenue'], 2) }}</h3>
                    </div>
                    <div class="bg-light p-3 rounded-circle text-success fs-4"><i class="fa fa-dollar-sign"></i></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3" style="border-radius: 0; border-left: 4px solid #3b82f6 !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-medium">TOTAL ORDERS</span>
                        <h3 class="fw-bold text-dark mt-1 mb-0">{{ $analytics['overview']['total_orders'] }}</h3>
                    </div>
                    <div class="bg-light p-3 rounded-circle text-primary fs-4"><i class="fa fa-cart-shopping"></i></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3" style="border-radius: 0; border-left: 4px solid #8b5cf6 !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-medium">AVERAGE ORDER VALUE</span>
                        <h3 class="fw-bold text-dark mt-1 mb-0">${{ number_format($analytics['overview']['average_order_value'], 2) }}</h3>
                    </div>
                    <div class="bg-light p-3 rounded-circle text-purple fs-4"><i class="fa fa-chart-line"></i></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3" style="border-radius: 0; border-left: 4px solid #f59e0b !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-medium">PENDING ORDERS</span>
                        <h3 class="fw-bold text-dark mt-1 mb-0">{{ $analytics['overview']['pending_orders'] }}</h3>
                    </div>
                    <div class="bg-light p-3 rounded-circle text-warning fs-4"><i class="fa fa-clock"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Selling Products Table -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
        <div class="card-header bg-transparent border-0 pt-3 pb-0 px-3">
            <h5 class="card-title mb-0 fw-bold text-dark">Top 5 Best Selling Products</h5>
        </div>
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Product Name</th>
                            <th>Total Units Sold</th>
                            <th>Total Sales Volume</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($analytics['top_selling_products'] as $item)
                        <tr>
                            <td class="fw-bold">{{ $item->product_name }}</td>
                            <td><span class="badge bg-info">{{ $item->total_sold }} units</span></td>
                            <td class="text-success fw-bold">${{ number_format($item->total_revenue, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-3">No sales records available yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>
