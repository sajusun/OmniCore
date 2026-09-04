<x-admin-layout>
    <x-slot name="title">Create Product</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Product</h2>
    </x-slot>

    <div class="container-fluid py-2 px-0">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
            <div>
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb mb-0" style="font-size: 0.875rem;">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}" class="text-decoration-none text-muted">Products</a></li>
                        <li class="breadcrumb-item active text-dark fw-medium">Create</li>
                    </ol>
                </nav>
                <h4 class="fw-bold text-dark mb-0">Add New Product</h4>
            </div>
            <div>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                    <i class="fa fa-arrow-left me-1"></i> Back to Products
                </a>
            </div>
        </div>

        @if(isset($errors) && $errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="fa fa-exclamation-triangle me-2"></i> Please fix the errors below:
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="productCreateForm">
            @csrf

            <div class="row g-4">
                {{-- Left Column: Main Info --}}
                <div class="col-lg-8">
                    {{-- 1. General Info --}}
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
                        <div class="card-header bg-transparent border-bottom py-3">
                            <h5 class="card-title mb-0 fw-bold text-dark"><i class="fa fa-info-circle me-2 text-primary"></i> Basic Information</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Product Title <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Wireless Noise-Cancelling Headphones" required>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Product Type <span class="text-danger">*</span></label>
                                    <select name="type" id="productType" class="form-select" required>
                                        <option value="simple" {{ old('type') == 'simple' ? 'selected' : '' }}>Simple Product</option>
                                        <option value="variable" {{ old('type') == 'variable' ? 'selected' : '' }}>Variable Product (Variants)</option>
                                        <option value="digital" {{ old('type') == 'digital' ? 'selected' : '' }}>Digital / Downloadable</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">SKU (Stock Keeping Unit)</label>
                                    <input type="text" name="sku" class="form-control" value="{{ old('sku') }}" placeholder="Auto-generated if empty">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Short Summary</label>
                                <textarea name="short_description" rows="2" class="form-control" placeholder="Brief 1-2 sentence product highlight...">{{ old('short_description') }}</textarea>
                            </div>

                            <div class="mb-0">
                                <label class="form-label fw-bold">Detailed Description</label>
                                <textarea name="description" rows="6" class="form-control" placeholder="Full product specifications, features, and details...">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Pricing & Cost --}}
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
                        <div class="card-header bg-transparent border-bottom py-3">
                            <h5 class="card-title mb-0 fw-bold text-dark"><i class="fa fa-tag me-2 text-success"></i> Pricing & Cost</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Selling Price ($) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" min="0" name="price" class="form-control" value="{{ old('price', '0.00') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Original / Compare Price ($)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" min="0" name="compare_at_price" class="form-control" value="{{ old('compare_at_price') }}" placeholder="Strike-through price">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Cost per Item ($)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" min="0" name="cost_price" class="form-control" value="{{ old('cost_price') }}" placeholder="For profit tracking">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Inventory Tracking --}}
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
                        <div class="card-header bg-transparent border-bottom py-3">
                            <h5 class="card-title mb-0 fw-bold text-dark"><i class="fa fa-warehouse me-2 text-warning"></i> Inventory & Stock</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="manage_stock" id="manageStock" value="1" {{ old('manage_stock', 1) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="manageStock">Track Stock Quantity for this Product</label>
                            </div>

                            <div class="row g-3" id="stockInputs">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Available Quantity</label>
                                    <input type="number" min="0" name="stock_quantity" class="form-control" value="{{ old('stock_quantity', 20) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Low Stock Warning Threshold</label>
                                    <input type="number" min="0" name="low_stock_threshold" class="form-control" value="{{ old('low_stock_threshold', 5) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Variants Section (For Variable Product) --}}
                    <div class="card border-0 shadow-sm mb-4" id="variantsCard" style="display: none; border-radius: 0;">
                        <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0 fw-bold text-dark"><i class="fa fa-sliders me-2 text-info"></i> Product Variants Matrix</h5>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addVariantRow()"><i class="fa fa-plus me-1"></i> Add Variant Option</button>
                        </div>
                        <div class="card-body p-4">
                            <p class="small text-muted mb-3">Define variant variations (e.g. Color, Size, Capacity) with their specific prices and stock counts.</p>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle" id="variantsTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Variant Title (e.g. Red / XL)</th>
                                            <th>SKU</th>
                                            <th>Price ($)</th>
                                            <th>Stock Qty</th>
                                            <th style="width: 50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="variantRowsContainer">
                                        {{-- Dynamic Rows --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- 5. SEO Metadata --}}
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
                        <div class="card-header bg-transparent border-bottom py-3">
                            <h5 class="card-title mb-0 fw-bold text-dark"><i class="fa fa-magnifying-glass me-2 text-secondary"></i> Search Engine Optimization (SEO)</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Meta Title</label>
                                <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title') }}" placeholder="SEO Title tag (e.g. Buy Premium Wireless Headphones Online)">
                            </div>
                            <div class="mb-0">
                                <label class="form-label fw-bold">Meta Description</label>
                                <textarea name="meta_description" rows="2" class="form-control" placeholder="Brief search engine meta snippet...">{{ old('meta_description') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Media, Organization & Publishing --}}
                <div class="col-lg-4">
                    {{-- 1. Publishing Status --}}
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
                        <div class="card-header bg-transparent border-bottom py-3">
                            <h5 class="card-title mb-0 fw-bold text-dark"><i class="fa fa-paper-plane me-2 text-primary"></i> Publishing</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="published" {{ old('status', 'published') == 'published' ? 'selected' : '' }}>Published (Active)</option>
                                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft (Hidden)</option>
                                    <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                                </select>
                            </div>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="is_featured" id="isFeatured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="isFeatured">Mark as Featured Product</label>
                            </div>

                            <div class="form-check form-switch mb-4">
                                <input class="form-check-input" type="checkbox" name="is_refundable" id="isRefundable" value="1" {{ old('is_refundable', 1) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="isRefundable">Eligible for Return / Refund</label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                                <i class="fa fa-save me-1"></i> Save & Publish Product
                            </button>
                        </div>
                    </div>

                    {{-- 2. Organization: Category & Brand --}}
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
                        <div class="card-header bg-transparent border-bottom py-3">
                            <h5 class="card-title mb-0 fw-bold text-dark"><i class="fa fa-layer-group me-2 text-info"></i> Organization</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Primary Category</label>
                                <select name="category_id" class="form-select">
                                    <option value="">Select Category (None)</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                        @foreach($cat->children as $child)
                                            <option value="{{ $child->id }}" {{ old('category_id') == $child->id ? 'selected' : '' }}>
                                                &nbsp;&nbsp;-- {{ $child->name }}
                                            </option>
                                        @endforeach
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Brand</label>
                                <select name="brand_id" class="form-select">
                                    <option value="">Select Brand (None)</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-0">
                                <label class="form-label fw-bold">Tags (Comma Separated)</label>
                                <input type="text" name="tags" class="form-control" value="{{ old('tags') }}" placeholder="e.g. Wireless, Audio, Gadgets">
                            </div>
                        </div>
                    </div>

                    {{-- 3. Product Media --}}
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
                        <div class="card-header bg-transparent border-bottom py-3">
                            <h5 class="card-title mb-0 fw-bold text-dark"><i class="fa fa-image me-2 text-secondary"></i> Media & Gallery</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Main Product Thumbnail <span class="text-danger">*</span></label>
                                <input type="file" name="thumbnail" class="form-control" accept="image/*" onchange="previewMainThumb(this)">
                                <div id="thumbPreviewBox" class="mt-2 text-center" style="display: none;">
                                    <img id="thumbPreviewImg" src="" class="rounded border" style="max-height: 120px; object-fit: cover;">
                                </div>
                            </div>

                            <div class="mb-0">
                                <label class="form-label fw-bold">Additional Gallery Images</label>
                                <input type="file" name="gallery[]" class="form-control" accept="image/*" multiple>
                                <small class="text-muted">You can select multiple photos at once.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        // Toggle Stock Input
        $('#manageStock').on('change', function() {
            if ($(this).is(':checked')) {
                $('#stockInputs').slideDown();
            } else {
                $('#stockInputs').slideUp();
            }
        });

        // Toggle Variants Card
        $('#productType').on('change', function() {
            if ($(this).val() === 'variable') {
                $('#variantsCard').slideDown();
                if ($('#variantRowsContainer tr').length === 0) {
                    addVariantRow('Default Variant', 10);
                }
            } else {
                $('#variantsCard').slideUp();
            }
        });

        let variantIndex = 0;
        function addVariantRow(name = '', stock = 10) {
            variantIndex++;
            const rowHtml = `
                <tr id="variantRow_${variantIndex}">
                    <td>
                        <input type="text" name="variants[${variantIndex}][name]" class="form-control form-control-sm" value="${name}" placeholder="e.g. Red / 64GB" required>
                    </td>
                    <td>
                        <input type="text" name="variants[${variantIndex}][sku]" class="form-control form-control-sm" placeholder="Optional SKU">
                    </td>
                    <td>
                        <input type="number" step="0.01" min="0" name="variants[${variantIndex}][price]" class="form-control form-control-sm" placeholder="Price" value="">
                    </td>
                    <td>
                        <input type="number" min="0" name="variants[${variantIndex}][stock_quantity]" class="form-control form-control-sm" value="${stock}" required>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="$('#variantRow_${variantIndex}').remove()"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
            `;
            $('#variantRowsContainer').append(rowHtml);
        }

        function previewMainThumb(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#thumbPreviewImg').attr('src', e.target.result);
                    $('#thumbPreviewBox').show();
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    @endpush
</x-admin-layout>
