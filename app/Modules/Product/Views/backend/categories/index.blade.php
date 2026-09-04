<x-admin-layout>
    <x-slot name="title">Product Categories</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Product Categories</h2>
    </x-slot>

    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.875rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}" class="text-decoration-none text-muted">Products</a></li>
                    <li class="breadcrumb-item active text-dark fw-medium">Categories</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0">Categories Management</h4>
        </div>
        <div>
            <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2" onclick="openCreateCategoryModal()">
                <i class="fa fa-plus"></i> Add New Category
            </button>
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
                <i class="fa fa-folder-tree me-2 text-primary"></i> All Categories ({{ \App\Modules\Product\Models\ProductCategory::count() }})
            </h5>
        </div>
        <div class="card-body p-3">
            <x-datatable id="categories-datatable" :url="route('admin.categories.index')" :order="[[0, 'asc']]" :columns="[
                ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'SL', 'orderable' => false, 'searchable' => false],
                ['data' => 'name_display', 'name' => 'name', 'title' => 'Category Name'],
                ['data' => 'parent', 'name' => 'parent.name', 'title' => 'Parent Category'],
                ['data' => 'products_count', 'name' => 'products_count', 'title' => 'Total Products', 'searchable' => false],
                ['data' => 'status', 'name' => 'is_active', 'title' => 'Status'],
                ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false]
            ]" />
        </div>
    </div>

    {{-- Category Modal (Create / Edit) --}}
    <div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form id="categoryForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="categoryFormMethod" value="POST">
                    <input type="hidden" id="categoryId">

                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold" id="categoryModalTitle">Add New Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Category Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="categoryName" class="form-control" required placeholder="e.g. Smartphones & Tablets">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Parent Category</label>
                            <select name="parent_id" id="categoryParentId" class="form-select">
                                <option value="">None (Top-Level Category)</option>
                                @foreach($parentCategories as $pCat)
                                    <option value="{{ $pCat->id }}">{{ $pCat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Icon Class</label>
                                <input type="text" name="icon" id="categoryIcon" class="form-control" placeholder="fa fa-mobile-screen">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Commission Rate (%)</label>
                                <input type="number" step="0.01" min="0" max="100" name="commission_rate" id="categoryCommission" class="form-control" placeholder="e.g. 5.00">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" id="categoryDescription" rows="2" class="form-control" placeholder="Optional category overview..."></textarea>
                        </div>

                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" name="is_active" id="categoryIsActive" value="1" checked>
                            <label class="form-check-label fw-bold" for="categoryIsActive">Category is Active</label>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="saveCategoryBtn">Save Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const categoryModal = new bootstrap.Modal(document.getElementById('categoryModal'));

        function openCreateCategoryModal() {
            $('#categoryForm')[0].reset();
            $('#categoryFormMethod').val('POST');
            $('#categoryId').val('');
            $('#categoryModalTitle').text('Add New Category');
            $('#categoryForm').attr('action', "{{ route('admin.categories.store') }}");
            $('#categoryIsActive').prop('checked', true);
            categoryModal.show();
        }

        function openEditCategoryModal(data) {
            $('#categoryForm')[0].reset();
            $('#categoryFormMethod').val('PUT');
            $('#categoryId').val(data.id);
            $('#categoryModalTitle').text('Edit Category: ' + data.name);
            $('#categoryForm').attr('action', "{{ url('admin/categories') }}/" + data.id);
            $('#categoryName').val(data.name);
            $('#categoryParentId').val(data.parent_id || '');
            $('#categoryIcon').val(data.icon || '');
            $('#categoryCommission').val(data.commission_rate || '');
            $('#categoryDescription').val(data.description || '');
            $('#categoryIsActive').prop('checked', !!data.is_active);
            categoryModal.show();
        }

        $('#categoryForm').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const actionUrl = form.attr('action');

            $.ajax({
                url: actionUrl,
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    categoryModal.hide();
                    Swal.fire('Success!', response.message || 'Saved successfully.', 'success');
                    $('#categories-datatable').DataTable().ajax.reload(null, false);
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Validation failed.';
                    Swal.fire('Error!', msg, 'error');
                }
            });
        });

        function deleteCategory(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Subcategories will be converted to root categories.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('admin/categories') }}/" + id,
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(res) {
                            Swal.fire('Deleted!', res.message, 'success');
                            $('#categories-datatable').DataTable().ajax.reload(null, false);
                        },
                        error: function() {
                            Swal.fire('Error!', 'Could not delete category.', 'error');
                        }
                    });
                }
            });
        }
    </script>
    @endpush
</x-admin-layout>
