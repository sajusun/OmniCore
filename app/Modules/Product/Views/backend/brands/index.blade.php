<x-admin-layout>
    <x-slot name="title">Product Brands</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Product Brands</h2>
    </x-slot>

    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.875rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}" class="text-decoration-none text-muted">Products</a></li>
                    <li class="breadcrumb-item active text-dark fw-medium">Brands</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0">Brands Management</h4>
        </div>
        <div>
            <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2" onclick="openCreateBrandModal()">
                <i class="fa fa-plus"></i> Add New Brand
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
                <i class="fa fa-tag me-2 text-primary"></i> All Brands ({{ \App\Modules\Product\Models\ProductBrand::count() }})
            </h5>
        </div>
        <div class="card-body p-3">
            <x-datatable id="brands-datatable" :url="route('admin.brands.index')" :order="[[0, 'asc']]" :columns="[
                ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'SL', 'orderable' => false, 'searchable' => false],
                ['data' => 'name_display', 'name' => 'name', 'title' => 'Brand Name'],
                ['data' => 'website_link', 'name' => 'website', 'title' => 'Website'],
                ['data' => 'products_count', 'name' => 'products_count', 'title' => 'Total Products', 'searchable' => false],
                ['data' => 'status', 'name' => 'is_active', 'title' => 'Status'],
                ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false]
            ]" />
        </div>
    </div>

    {{-- Brand Modal --}}
    <div class="modal fade" id="brandModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form id="brandForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="brandFormMethod" value="POST">
                    <input type="hidden" id="brandId">

                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold" id="brandModalTitle">Add New Brand</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Brand Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="brandName" class="form-control" required placeholder="e.g. Sony, Apple, Nike">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Official Website URL</label>
                            <input type="url" name="website" id="brandWebsite" class="form-control" placeholder="https://www.brand.com">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" id="brandDescription" rows="2" class="form-control" placeholder="Optional brand biography..."></textarea>
                        </div>

                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="brandIsFeatured" value="1">
                            <label class="form-check-label fw-bold" for="brandIsFeatured">Feature this Brand</label>
                        </div>

                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" name="is_active" id="brandIsActive" value="1" checked>
                            <label class="form-check-label fw-bold" for="brandIsActive">Brand is Active</label>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="saveBrandBtn">Save Brand</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const brandModal = new bootstrap.Modal(document.getElementById('brandModal'));

        function openCreateBrandModal() {
            $('#brandForm')[0].reset();
            $('#brandFormMethod').val('POST');
            $('#brandId').val('');
            $('#brandModalTitle').text('Add New Brand');
            $('#brandForm').attr('action', "{{ route('admin.brands.store') }}");
            $('#brandIsActive').prop('checked', true);
            $('#brandIsFeatured').prop('checked', false);
            brandModal.show();
        }

        function openEditBrandModal(data) {
            $('#brandForm')[0].reset();
            $('#brandFormMethod').val('PUT');
            $('#brandId').val(data.id);
            $('#brandModalTitle').text('Edit Brand: ' + data.name);
            $('#brandForm').attr('action', "{{ url('admin/brands') }}/" + data.id);
            $('#brandName').val(data.name);
            $('#brandWebsite').val(data.website || '');
            $('#brandDescription').val(data.description || '');
            $('#brandIsActive').prop('checked', !!data.is_active);
            $('#brandIsFeatured').prop('checked', !!data.is_featured);
            brandModal.show();
        }

        $('#brandForm').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    brandModal.hide();
                    Swal.fire('Success!', response.message || 'Saved successfully.', 'success');
                    $('#brands-datatable').DataTable().ajax.reload(null, false);
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Validation failed.';
                    Swal.fire('Error!', msg, 'error');
                }
            });
        });

        function deleteBrand(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Delete this brand?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('admin/brands') }}/" + id,
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(res) {
                            Swal.fire('Deleted!', res.message, 'success');
                            $('#brands-datatable').DataTable().ajax.reload(null, false);
                        },
                        error: function() {
                            Swal.fire('Error!', 'Could not delete brand.', 'error');
                        }
                    });
                }
            });
        }
    </script>
    @endpush
</x-admin-layout>
