<x-admin-layout>
    <x-slot name="title">Product Attributes</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Product Attributes</h2>
    </x-slot>

    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.875rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}" class="text-decoration-none text-muted">Products</a></li>
                    <li class="breadcrumb-item active text-dark fw-medium">Attributes</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0">Attributes & Options Management</h4>
        </div>
        <div>
            <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2" onclick="openCreateAttributeModal()">
                <i class="fa fa-plus"></i> Add New Attribute
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
                <i class="fa fa-sliders me-2 text-primary"></i> Attributes List ({{ \App\Modules\Product\Models\ProductAttribute::count() }})
            </h5>
        </div>
        <div class="card-body p-3">
            <x-datatable id="attributes-datatable" :url="route('admin.attributes.index')" :order="[[0, 'asc']]" :columns="[
                ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'SL', 'orderable' => false, 'searchable' => false],
                ['data' => 'name_display', 'name' => 'name', 'title' => 'Attribute Name'],
                ['data' => 'type', 'name' => 'type', 'title' => 'Input Type'],
                ['data' => 'values_display', 'name' => 'values.value', 'title' => 'Defined Values / Options', 'orderable' => false],
                ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false]
            ]" />
        </div>
    </div>

    {{-- Attribute Modal --}}
    <div class="modal fade" id="attributeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form id="attributeForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="attributeFormMethod" value="POST">
                    <input type="hidden" id="attributeId">

                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold" id="attributeModalTitle">Add New Attribute</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Attribute Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="attributeName" class="form-control" required placeholder="e.g. Size, Color, Storage, Material">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Display Type <span class="text-danger">*</span></label>
                            <select name="type" id="attributeType" class="form-select" required>
                                <option value="select">Dropdown Select</option>
                                <option value="color">Color Palette (Hex Code)</option>
                                <option value="button">Button / Pill</option>
                                <option value="text">Text Option</option>
                            </select>
                        </div>

                        <div class="mb-0" id="initialValuesBox">
                            <label class="form-label fw-bold">Initial Values (Comma Separated)</label>
                            <input type="text" name="initial_values" class="form-control" placeholder="e.g. Small, Medium, Large, XL">
                            <small class="text-muted">You can also add more individual values later.</small>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="saveAttributeBtn">Save Attribute</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Add Value Modal --}}
    <div class="modal fade" id="valueModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow">
                <form id="valueForm" method="POST">
                    @csrf
                    <input type="hidden" id="valAttrId">

                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold" id="valueModalTitle">Add Option Value</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Value Name <span class="text-danger">*</span></label>
                            <input type="text" name="value" id="valInput" class="form-control" required placeholder="e.g. Red, XL, 256GB">
                        </div>

                        <div class="mb-0" id="colorCodeBox" style="display: none;">
                            <label class="form-label fw-bold">Color Hex Code</label>
                            <div class="input-group">
                                <input type="color" id="valColorPicker" class="form-control form-control-color" value="#000000">
                                <input type="text" name="code" id="valCodeInput" class="form-control" placeholder="#000000">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Add Value</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const attributeModal = new bootstrap.Modal(document.getElementById('attributeModal'));
        const valueModal = new bootstrap.Modal(document.getElementById('valueModal'));

        function openCreateAttributeModal() {
            $('#attributeForm')[0].reset();
            $('#attributeFormMethod').val('POST');
            $('#attributeId').val('');
            $('#initialValuesBox').show();
            $('#attributeModalTitle').text('Add New Attribute');
            $('#attributeForm').attr('action', "{{ route('admin.attributes.store') }}");
            attributeModal.show();
        }

        function openEditAttributeModal(data) {
            $('#attributeForm')[0].reset();
            $('#attributeFormMethod').val('PUT');
            $('#attributeId').val(data.id);
            $('#initialValuesBox').hide();
            $('#attributeModalTitle').text('Edit Attribute: ' + data.name);
            $('#attributeForm').attr('action', "{{ url('admin/attributes') }}/" + data.id);
            $('#attributeName').val(data.name);
            $('#attributeType').val(data.type);
            attributeModal.show();
        }

        function openAddValueModal(attrId, attrName, type) {
            $('#valueForm')[0].reset();
            $('#valAttrId').val(attrId);
            $('#valueModalTitle').text('Add to ' + attrName);
            $('#valueForm').attr('action', "{{ url('admin/attributes') }}/" + attrId + "/values");
            
            if (type === 'color') {
                $('#colorCodeBox').show();
            } else {
                $('#colorCodeBox').hide();
            }
            valueModal.show();
        }

        $('#valColorPicker').on('input', function() {
            $('#valCodeInput').val($(this).val());
        });

        $('#attributeForm').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    attributeModal.hide();
                    Swal.fire('Success!', response.message || 'Saved successfully.', 'success');
                    $('#attributes-datatable').DataTable().ajax.reload(null, false);
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Validation failed.';
                    Swal.fire('Error!', msg, 'error');
                }
            });
        });

        $('#valueForm').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    valueModal.hide();
                    Swal.fire('Success!', response.message, 'success');
                    $('#attributes-datatable').DataTable().ajax.reload(null, false);
                },
                error: function(xhr) {
                    Swal.fire('Error!', 'Could not add value.', 'error');
                }
            });
        });

        function deleteAttribute(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "All associated option values will be removed!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('admin/attributes') }}/" + id,
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(res) {
                            Swal.fire('Deleted!', res.message, 'success');
                            $('#attributes-datatable').DataTable().ajax.reload(null, false);
                        }
                    });
                }
            });
        }

        function deleteAttributeValue(valueId) {
            $.ajax({
                url: "{{ url('admin/attributes/values') }}/" + valueId,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(res) {
                    $('#attributes-datatable').DataTable().ajax.reload(null, false);
                }
            });
        }
    </script>
    @endpush
</x-admin-layout>
