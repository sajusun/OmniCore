<x-admin-layout>
    <x-slot name="title">Product Categories</x-slot>

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.875rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item active text-dark fw-medium">Categories</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0">Product Categories</h4>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
        <div class="card-body p-3">
            <x-datatable id="categories-datatable" :url="route('admin.categories.index')" :columns="[
                ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'SL', 'orderable' => false, 'searchable' => false],
                ['data' => 'name', 'name' => 'name', 'title' => 'Category Name'],
                ['data' => 'slug', 'name' => 'slug', 'title' => 'Slug'],
                ['data' => 'parent', 'name' => 'parent.name', 'title' => 'Parent Category'],
                ['data' => 'products_count', 'name' => 'products_count', 'title' => 'Products Count'],
                ['data' => 'status', 'name' => 'is_active', 'title' => 'Status'],
                ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false]
            ]" />
        </div>
    </div>
</x-admin-layout>
