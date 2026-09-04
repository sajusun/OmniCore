<x-admin-layout>
    <x-slot name="title">Low Stock Inventory Alerts</x-slot>

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.875rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item active text-dark fw-medium">Inventory</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0">Low Stock Inventory Alerts</h4>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
        <div class="card-body p-3">
            <x-datatable id="inventory-datatable" :url="route('admin.inventory.index')" :columns="[
                ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'SL', 'orderable' => false, 'searchable' => false],
                ['data' => 'name', 'name' => 'name', 'title' => 'Product / SKU'],
                ['data' => 'category', 'name' => 'category.name', 'title' => 'Category'],
                ['data' => 'price', 'name' => 'price', 'title' => 'Unit Price'],
                ['data' => 'stock_status', 'name' => 'stock_quantity', 'title' => 'Stock Level']
            ]" />
        </div>
    </div>
</x-admin-layout>
