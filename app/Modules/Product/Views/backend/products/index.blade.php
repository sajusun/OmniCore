<x-admin-layout>
    <x-slot name="title">Products Management</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Products Management</h2>
    </x-slot>

    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.875rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item active text-dark fw-medium">Products</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0">Products Catalog</h4>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2">
                <i class="fa fa-plus"></i> Add New Product
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
        <div class="card-header bg-transparent border-0 pt-3 pb-0 px-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-bold text-dark">
                <i class="fa fa-boxes-stacked me-2 text-primary"></i> All Products ({{ \App\Modules\Product\Models\Product::count() }})
            </h5>
        </div>
        <div class="card-body p-3">
            <x-datatable id="products-datatable" :url="route('admin.products.index')" :order="[[0, 'desc']]" :columns="[
                ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'SL', 'orderable' => false, 'searchable' => false],
                ['data' => 'thumbnail', 'name' => 'thumbnail', 'title' => 'Image', 'orderable' => false, 'searchable' => false],
                ['data' => 'name_info', 'name' => 'name', 'title' => 'Product Details'],
                ['data' => 'category', 'name' => 'category.name', 'title' => 'Category'],
                ['data' => 'brand', 'name' => 'brand.name', 'title' => 'Brand'],
                ['data' => 'price_display', 'name' => 'price', 'title' => 'Price'],
                ['data' => 'stock', 'name' => 'stock_quantity', 'title' => 'Stock Status'],
                ['data' => 'type', 'name' => 'type', 'title' => 'Type'],
                ['data' => 'status', 'name' => 'status', 'title' => 'Status'],
                ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false]
            ]" />
        </div>
    </div>

    @push('scripts')
    <script>
        function deleteProduct(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This product and its variants will be removed!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('admin/products') }}/" + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Deleted!', response.message, 'success');
                                $('#products-datatable').DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire('Error!', response.message || 'Operation failed', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'An error occurred while deleting.', 'error');
                        }
                    });
                }
            });
        }
    </script>
    @endpush
</x-admin-layout>
