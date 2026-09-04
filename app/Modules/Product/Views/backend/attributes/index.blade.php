<x-admin-layout>
    <x-slot name="title">Product Attributes & Options</x-slot>

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.875rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item active text-dark fw-medium">Attributes</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0">Attributes & Variant Options</h4>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
        <div class="card-body p-3">
            <x-datatable id="attributes-datatable" :url="route('admin.attributes.index')" :columns="[
                ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'SL', 'orderable' => false, 'searchable' => false],
                ['data' => 'name', 'name' => 'name', 'title' => 'Attribute Name'],
                ['data' => 'slug', 'name' => 'slug', 'title' => 'Slug'],
                ['data' => 'type', 'name' => 'type', 'title' => 'Input Type'],
                ['data' => 'values', 'name' => 'values', 'title' => 'Configured Values', 'orderable' => false]
            ]" />
        </div>
    </div>
</x-admin-layout>
