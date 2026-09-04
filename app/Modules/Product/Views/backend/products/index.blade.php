<x-admin-layout>
    <x-slot name="title">Products Management</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Products Management</h2>
    </x-slot>

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.875rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item active text-dark fw-medium">Products</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0">Products Catalog</h4>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
        <div class="card-header bg-transparent border-0 pt-3 pb-0 px-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-bold text-dark">All Products ({{ \App\Modules\Product\Models\Product::count() }})</h5>
        </div>
        <div class="card-body p-3">
            <x-datatable id="products-datatable" :url="route('admin.products.index')" :order="[[0, 'desc']]" :columns="[
                ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'SL', 'orderable' => false, 'searchable' => false],
                ['data' => 'thumbnail', 'name' => 'thumbnail', 'title' => 'Image', 'orderable' => false, 'searchable' => false],
                ['data' => 'name', 'name' => 'name', 'title' => 'Product Name'],
                ['data' => 'sku', 'name' => 'sku', 'title' => 'SKU'],
                ['data' => 'category', 'name' => 'category.name', 'title' => 'Category'],
                ['data' => 'brand', 'name' => 'brand.name', 'title' => 'Brand'],
                ['data' => 'price', 'name' => 'price', 'title' => 'Price'],
                ['data' => 'stock', 'name' => 'stock_quantity', 'title' => 'Stock Status'],
                ['data' => 'type', 'name' => 'type', 'title' => 'Type'],
                ['data' => 'status', 'name' => 'status', 'title' => 'Status'],
                ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false]
            ]" />
        </div>
    </div>
</x-admin-layout>
