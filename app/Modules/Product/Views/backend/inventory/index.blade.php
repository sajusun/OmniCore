<x-admin-layout>
    <x-slot name="title">Low Stock Inventory Alerts</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Inventory Management</h2>
    </x-slot>

    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.875rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}" class="text-decoration-none text-muted">Products</a></li>
                    <li class="breadcrumb-item active text-dark fw-medium">Inventory Alerts</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0">Low Stock & Out of Stock Inventory</h4>
        </div>
        <div>
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                <i class="fa fa-boxes-stacked me-1"></i> All Products
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
        <div class="card-header bg-transparent border-0 pt-3 pb-0 px-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-bold text-dark">
                <i class="fa fa-triangle-exclamation me-2 text-warning"></i> Products Requiring Stock Attention
            </h5>
        </div>
        <div class="card-body p-3">
            <x-datatable id="inventory-datatable" :url="route('admin.inventory.index')" :order="[[3, 'asc']]" :columns="[
                ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'SL', 'orderable' => false, 'searchable' => false],
                ['data' => 'name_info', 'name' => 'name', 'title' => 'Product Details'],
                ['data' => 'category', 'name' => 'category.name', 'title' => 'Category'],
                ['data' => 'current_stock', 'name' => 'stock_quantity', 'title' => 'Current Stock'],
                ['data' => 'threshold', 'name' => 'low_stock_threshold', 'title' => 'Low Threshold'],
                ['data' => 'action', 'name' => 'action', 'title' => 'Quick Action', 'orderable' => false, 'searchable' => false]
            ]" />
        </div>
    </div>

    {{-- Restock Modal --}}
    <div class="modal fade" id="restockModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow">
                <form id="restockForm">
                    @csrf
                    <input type="hidden" name="product_id" id="restockProductId">

                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold" id="restockTitle">Adjust Stock</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="small text-muted mb-3" id="restockProductName"></p>

                        <div class="mb-0">
                            <label class="form-label fw-bold">New Total Stock Quantity <span class="text-danger">*</span></label>
                            <input type="number" min="0" name="stock_quantity" id="restockQuantityInput" class="form-control form-control-lg text-center fw-bold text-success" required>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Update Stock</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const restockModal = new bootstrap.Modal(document.getElementById('restockModal'));

        function openRestockModal(productId, productName, currentQty) {
            $('#restockProductId').val(productId);
            $('#restockProductName').text(productName);
            $('#restockQuantityInput').val(currentQty);
            restockModal.show();
        }

        $('#restockForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('admin.inventory.update-stock') }}",
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    restockModal.hide();
                    Swal.fire('Updated!', response.message, 'success');
                    $('#inventory-datatable').DataTable().ajax.reload(null, false);
                },
                error: function() {
                    Swal.fire('Error!', 'Failed to update stock.', 'error');
                }
            });
        });
    </script>
    @endpush
</x-admin-layout>
