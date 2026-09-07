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
            <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm" onclick="openCreateAttributeModal()">
                <i class="fa fa-plus"></i> Add New Attribute
            </button>
        </div>
    </div>

    {{-- Status Notification Modal --}}
    <x-modal.status />

    <x-card title="Attributes List ({{ \App\Modules\Product\Models\ProductAttribute::count() }})">
        <x-datatable id="attributes-datatable" :url="route('admin.attributes.index')" :order="[[0, 'asc']]" :columns="[
            ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'SL', 'orderable' => false, 'searchable' => false],
            ['data' => 'name_display', 'name' => 'name', 'title' => 'Attribute Name'],
            ['data' => 'type', 'name' => 'type', 'title' => 'Input Type'],
            ['data' => 'values_display', 'name' => 'values.value', 'title' => 'Defined Values / Options', 'orderable' => false],
            ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false]
        ]" />
    </x-card>

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
                            <input type="text" name="name" id="attrName" class="form-control" required placeholder="e.g. Color, Size, Storage Capacity">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Display Type <span class="text-danger">*</span></label>
                            <select name="type" id="attrType" class="form-select" required>
                                <option value="select">Dropdown Select</option>
                                <option value="color">Color Swatch</option>
                                <option value="button">Pill Button / Text Badge</option>
                                <option value="radio">Radio Option</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Initial Values (Optional)</label>
                            <input type="text" name="values" id="attrValues" class="form-control" placeholder="Comma separated, e.g. Red, Blue, Green or 64GB, 128GB">
                            <small class="text-muted">Separate multiple values with commas.</small>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="saveAttrBtn">Save Attribute</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Manage Attribute Values Modal --}}
    <div class="modal fade" id="valuesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold" id="valuesModalTitle">Manage Options</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="addValueForm" class="d-flex gap-2 mb-4">
                        @csrf
                        <input type="hidden" id="valAttrId">
                        <input type="text" id="valName" class="form-control" placeholder="New option value (e.g. XL, #FF0000)" required>
                        <input type="color" id="valColorCode" class="form-control form-control-color d-none" value="#000000" title="Choose color">
                        <button type="submit" class="btn btn-primary px-4 text-nowrap"><i class="fa fa-plus me-1"></i> Add Option</button>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Option Value</th>
                                    <th>Color Code / Extra</th>
                                    <th class="text-end" style="width: 80px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="valuesTableBody">
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Loading options...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const attributeModal = new bootstrap.Modal(document.getElementById('attributeModal'));
        const valuesModal = new bootstrap.Modal(document.getElementById('valuesModal'));

        function openCreateAttributeModal() {
            $('#attributeForm')[0].reset();
            $('#attributeFormMethod').val('POST');
            $('#attributeId').val('');
            $('#attributeModalTitle').text('Create Product Attribute');
            $('#attributeForm').attr('action', "{{ route('admin.attributes.store') }}");
            attributeModal.show();
        }

        function openEditAttributeModal(data) {
            $('#attributeForm')[0].reset();
            $('#attributeFormMethod').val('PUT');
            $('#attributeId').val(data.id);
            $('#attributeModalTitle').text('Edit Attribute: ' + data.name);
            $('#attributeForm').attr('action', "{{ url('admin/attributes') }}/" + data.id);
            $('#attrName').val(data.name);
            $('#attrType').val(data.type);
            $('#attrValues').val('');
            attributeModal.show();
        }

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

        function deleteAttribute(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Delete this attribute and all associated option values?",
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
                        },
                        error: function() {
                            Swal.fire('Error!', 'Could not delete attribute.', 'error');
                        }
                    });
                }
            });
        }

        function openValuesModal(attrId, attrName, attrType) {
            $('#valAttrId').val(attrId);
            $('#valuesModalTitle').text('Manage Options for: ' + attrName);
            if (attrType === 'color') {
                $('#valColorCode').removeClass('d-none');
            } else {
                $('#valColorCode').addClass('d-none');
            }
            loadAttributeValues(attrId);
            valuesModal.show();
        }

        function loadAttributeValues(attrId) {
            $('#valuesTableBody').html('<tr><td colspan="3" class="text-center text-muted">Loading...</td></tr>');
            $.get("{{ url('admin/attributes') }}/" + attrId + "/values", function(data) {
                let html = '';
                if (data && data.length > 0) {
                    data.forEach(function(val) {
                        html += `
                            <tr>
                                <td class="fw-bold">${val.value}</td>
                                <td>${val.color_code ? '<span class="d-inline-block border rounded me-1" style="width:16px;height:16px;background-color:'+val.color_code+'"></span> ' + val.color_code : '-'}</td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteAttributeValue(${val.id})">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    html = '<tr><td colspan="3" class="text-center text-muted">No values defined yet. Add one above!</td></tr>';
                }
                $('#valuesTableBody').html(html);
            });
        }

        $('#addValueForm').on('submit', function(e) {
            e.preventDefault();
            const attrId = $('#valAttrId').val();
            const value = $('#valName').val();
            const colorCode = !$('#valColorCode').hasClass('d-none') ? $('#valColorCode').val() : null;

            $.post("{{ url('admin/attributes') }}/" + attrId + "/values", {
                _token: '{{ csrf_token() }}',
                value: value,
                color_code: colorCode
            }, function(res) {
                $('#valName').val('');
                loadAttributeValues(attrId);
                $('#attributes-datatable').DataTable().ajax.reload(null, false);
            }).fail(function(xhr) {
                Swal.fire('Error!', xhr.responseJSON?.message || 'Failed to add option.', 'error');
            });
        });

        function deleteAttributeValue(valId) {
            const attrId = $('#valAttrId').val();
            $.ajax({
                url: "{{ url('admin/attributes/values') }}/" + valId,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function() {
                    loadAttributeValues(attrId);
                    $('#attributes-datatable').DataTable().ajax.reload(null, false);
                }
            });
        }
    </script>
    @endpush
</x-admin-layout>
