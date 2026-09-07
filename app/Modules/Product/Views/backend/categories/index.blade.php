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
            <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm" onclick="openCreateCategoryModal()">
                <i class="fa fa-plus"></i> Add New Category
            </button>
        </div>
    </div>

    {{-- Status Notification Modal --}}
    <x-modal.status />

    <x-card title="All Categories ({{ \App\Modules\Product\Models\ProductCategory::count() }})">
        <x-datatable id="categories-datatable" :url="route('admin.categories.index')" :order="[[0, 'asc']]" :columns="[
            ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'SL', 'orderable' => false, 'searchable' => false],
            ['data' => 'name_display', 'name' => 'name', 'title' => 'Category Name'],
            ['data' => 'parent', 'name' => 'parent.name', 'title' => 'Parent Category'],
            ['data' => 'products_count', 'name' => 'products_count', 'title' => 'Total Products', 'searchable' => false],
            ['data' => 'status', 'name' => 'is_active', 'title' => 'Status'],
            ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false]
        ]" />
    </x-card>

    {{-- Category Modal (Create / Edit) --}}
    <div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form id="categoryForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" id="categoryFormMethod" value="POST">
                    <input type="hidden" id="categoryId">

                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold" id="categoryModalTitle">Create Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Category Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="catName" class="form-control" required placeholder="e.g. Electronics, Fashion">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Parent Category</label>
                            <select name="parent_id" id="catParentId" class="form-select">
                                <option value="">None (Top-Level Category)</option>
                                @foreach(\App\Modules\Product\Models\ProductCategory::whereNull('parent_id')->orderBy('name')->get() as $pCat)
                                    <option value="{{ $pCat->id }}">{{ $pCat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" id="catDescription" rows="2" class="form-control" placeholder="Short description..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Category Icon / Image</label>
                            <input type="file" name="icon" id="catIcon" class="form-control" accept="image/*">
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="catIsActive" value="1" checked>
                                    <label class="form-check-label fw-bold" for="catIsActive">Active Status</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_featured" id="catIsFeatured" value="1">
                                    <label class="form-check-label fw-bold" for="catIsFeatured">Featured</label>
                                </div>
                            </div>
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
            $('#categoryModalTitle').text('Create Product Category');
            $('#categoryForm').attr('action', "{{ route('admin.categories.store') }}");
            $('#catIsActive').prop('checked', true);
            categoryModal.show();
        }

        function openEditCategoryModal(data) {
            $('#categoryForm')[0].reset();
            $('#categoryFormMethod').val('PUT');
            $('#categoryId').val(data.id);
            $('#categoryModalTitle').text('Edit Category: ' + data.name);
            $('#categoryForm').attr('action', "{{ url('admin/categories') }}/" + data.id);
            $('#catName').val(data.name);
            $('#catParentId').val(data.parent_id || '');
            $('#catDescription').val(data.description || '');
            $('#catIsActive').prop('checked', !!data.is_active);
            $('#catIsFeatured').prop('checked', !!data.is_featured);
            categoryModal.show();
        }

        $('#categoryForm').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const formData = new FormData(this);

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
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
                text: "Delete this category and detach related products?",
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
