<x-admin-layout>
    <x-slot name="title">Create Product (Wizard)</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Product</h2>
    </x-slot>

    <div class="container-fluid py-2 px-0">
        {{-- Breadcrumb & Header --}}
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
            <div>
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb mb-0" style="font-size: 0.875rem;">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}" class="text-decoration-none text-muted">Products</a></li>
                        <li class="breadcrumb-item active text-dark fw-medium">Create Wizard</li>
                    </ol>
                </nav>
                <h4 class="fw-bold text-dark mb-0">Create New Product</h4>
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

        {{-- Wizard Card Container --}}
        <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 0 !important;">
            {{-- Wizard Step Navigation Bar --}}
            <div class="card-header bg-white border-bottom p-0">
                <ul class="nav nav-pills nav-justified wizard-steps flex-column flex-md-row" id="productWizardNav" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active py-3 d-flex align-items-center justify-content-center gap-2" id="step1-tab" data-bs-toggle="tab" data-bs-target="#step1" type="button" role="tab">
                            <span class="step-num">1</span>
                            <span class="step-label text-start">
                                <span class="d-block fw-bold">General Info</span>
                                <small class="text-muted d-none d-lg-inline">Title, Category & Type</small>
                            </span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-3 d-flex align-items-center justify-content-center gap-2" id="step2-tab" data-bs-toggle="tab" data-bs-target="#step2" type="button" role="tab">
                            <span class="step-num">2</span>
                            <span class="step-label text-start">
                                <span class="d-block fw-bold">Pricing & Stock</span>
                                <small class="text-muted d-none d-lg-inline">Prices, SKU & Inventory</small>
                            </span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-3 d-flex align-items-center justify-content-center gap-2" id="step3-tab" data-bs-toggle="tab" data-bs-target="#step3" type="button" role="tab">
                            <span class="step-num">3</span>
                            <span class="step-label text-start">
                                <span class="d-block fw-bold">Variants Matrix</span>
                                <small class="text-muted d-none d-lg-inline">Options, Sizes, Colors</small>
                            </span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-3 d-flex align-items-center justify-content-center gap-2" id="step4-tab" data-bs-toggle="tab" data-bs-target="#step4" type="button" role="tab">
                            <span class="step-num">4</span>
                            <span class="step-label text-start">
                                <span class="d-block fw-bold">Media & Gallery</span>
                                <small class="text-muted d-none d-lg-inline">Thumbnails & Photos</small>
                            </span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-3 d-flex align-items-center justify-content-center gap-2" id="step5-tab" data-bs-toggle="tab" data-bs-target="#step5" type="button" role="tab">
                            <span class="step-num">5</span>
                            <span class="step-label text-start">
                                <span class="d-block fw-bold">SEO & Publish</span>
                                <small class="text-muted d-none d-lg-inline">Meta Tags & Launch</small>
                            </span>
                        </button>
                    </li>
                </ul>
            </div>

            {{-- Form Start --}}
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="productWizardForm">
                @csrf

                <div class="card-body p-4">
                    <div class="tab-content" id="productWizardContent">
                        
                        {{-- ==================== STEP 1: GENERAL INFO ==================== --}}
                        <div class="tab-pane fade show active" id="step1" role="tabpanel">
                            <div class="row g-4">
                                <div class="col-lg-8">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Product Title <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="productNameInput" class="form-control form-control-lg" value="{{ old('name') }}" placeholder="e.g. Wireless Bluetooth Noise Cancelling Headphones" required>
                                        <div class="form-text">Choose a unique, descriptive title that customers will search for.</div>
                                    </div>

                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Product Type <span class="text-danger">*</span></label>
                                            <select name="type" id="productTypeSelect" class="form-select" required>
                                                <option value="simple" {{ old('type') == 'simple' ? 'selected' : '' }}>Simple Product (Standard single item)</option>
                                                <option value="variable" {{ old('type') == 'variable' ? 'selected' : '' }}>Variable Product (With colors, sizes, specs)</option>
                                                <option value="digital" {{ old('type') == 'digital' ? 'selected' : '' }}>Digital / Downloadable</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Custom URL Slug</label>
                                            <input type="text" name="slug" class="form-control" placeholder="Auto-generated from title if empty">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Short Highlight Summary</label>
                                        <textarea name="short_description" rows="2" class="form-control" placeholder="Key 1-2 sentence highlight for search & quick view cards...">{{ old('short_description') }}</textarea>
                                    </div>

                                    <div class="mb-0">
                                        <label class="form-label fw-bold">Full Detailed Description</label>
                                        <textarea name="description" rows="7" class="form-control" placeholder="Comprehensive product specifications, features, materials, warranty, and package contents...">{{ old('description') }}</textarea>
                                    </div>
                                </div>

                                <div class="col-lg-4 border-start-lg">
                                    <div class="p-3 bg-light mb-4">
                                        <h6 class="fw-bold text-dark mb-3"><i class="fa fa-folder me-2 text-primary"></i> Taxonomy & Organization</h6>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Category</label>
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
                                            <input type="text" name="tags" class="form-control" value="{{ old('tags') }}" placeholder="e.g. Wireless, Gaming, Bluetooth">
                                        </div>
                                    </div>

                                    <div class="alert alert-info border-0 shadow-sm small mb-0">
                                        <i class="fa fa-lightbulb me-1"></i> <strong>Tip:</strong> If you select <strong>Variable Product</strong>, you'll configure individual variant prices and stocks in <strong>Step 3</strong>.
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ==================== STEP 2: PRICING & INVENTORY ==================== --}}
                        <div class="tab-pane fade" id="step2" role="tabpanel">
                            <div class="row g-4">
                                <div class="col-lg-7">
                                    <div class="card border border-light-subtle shadow-none p-3 mb-4" style="border-radius: 0;">
                                        <h6 class="fw-bold text-dark mb-3"><i class="fa fa-dollar-sign text-success me-2"></i> Price Settings</h6>
                                        
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Regular Selling Price ($) <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" step="0.01" min="0" name="price" id="basePriceInput" class="form-control form-control-lg fw-bold text-success" value="{{ old('price', '0.00') }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Compare at / Strike Price ($)</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" step="0.01" min="0" name="compare_at_price" class="form-control form-control-lg" value="{{ old('compare_at_price') }}" placeholder="Original price">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-0">
                                            <label class="form-label fw-bold">Cost per Item ($) <small class="text-muted fw-normal">(Optional, for profit calculations)</small></label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" step="0.01" min="0" name="cost_price" class="form-control" value="{{ old('cost_price') }}" placeholder="Cost to produce or purchase">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-5">
                                    <div class="card border border-light-subtle shadow-none p-3 mb-4" style="border-radius: 0;">
                                        <h6 class="fw-bold text-dark mb-3"><i class="fa fa-warehouse text-warning me-2"></i> Stock & SKU</h6>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Main Product SKU</label>
                                            <input type="text" name="sku" class="form-control font-monospace" value="{{ old('sku') }}" placeholder="Auto-generated if empty">
                                        </div>

                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" name="manage_stock" id="manageStockToggle" value="1" {{ old('manage_stock', 1) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="manageStockToggle">Track Stock Quantity</label>
                                        </div>

                                        <div id="stockFields">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Available Quantity in Stock</label>
                                                <input type="number" min="0" name="stock_quantity" class="form-control form-control-lg fw-bold text-primary" value="{{ old('stock_quantity', 20) }}">
                                            </div>
                                            <div class="mb-0">
                                                <label class="form-label fw-bold">Low Stock Alert Threshold</label>
                                                <input type="number" min="0" name="low_stock_threshold" class="form-control" value="{{ old('low_stock_threshold', 5) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ==================== STEP 3: VARIANTS MATRIX ==================== --}}
                        <div class="tab-pane fade" id="step3" role="tabpanel">
                            {{-- Notice for Simple Products --}}
                            <div id="simpleProductNotice" class="text-center py-4 bg-light border border-dashed mb-3">
                                <i class="fa fa-info-circle text-primary fs-3 mb-2"></i>
                                <h5 class="fw-bold text-dark mb-1">Simple Product Selected</h5>
                                <p class="text-muted small mb-3">This product is currently set as a Single / Simple item without variations. You can proceed directly to <strong>Media</strong>, or switch to <strong>Variable Product</strong> below.</p>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="enableVariableMode()">
                                    <i class="fa fa-sliders me-1"></i> Switch to Variable Product (Enable Variants)
                                </button>
                            </div>

                            {{-- Variable Product Matrix Builder --}}
                            <div id="variableProductSection" style="display: none;">
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
                                    <div>
                                        <h5 class="fw-bold text-dark mb-0"><i class="fa fa-sliders me-2 text-info"></i> Product Variants Configuration</h5>
                                        <small class="text-muted">Define individual SKU, pricing, and stock for each variation (e.g. Red / 64GB, Blue / 128GB).</small>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="addWizardVariantRow()">
                                            <i class="fa fa-plus me-1"></i> Add Variant Row
                                        </button>
                                    </div>
                                </div>

                                <div class="table-responsive bg-white border">
                                    <table class="table table-hover align-middle mb-0" id="wizardVariantsTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="min-width: 220px;">Variant Title / Combination</th>
                                                <th style="min-width: 150px;">SKU</th>
                                                <th style="min-width: 130px;">Price ($)</th>
                                                <th style="min-width: 110px;">Stock Qty</th>
                                                <th style="width: 50px;" class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="wizardVariantRows">
                                            {{-- Dynamic Variant Rows --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- ==================== STEP 4: MEDIA & GALLERY ==================== --}}
                        <div class="tab-pane fade" id="step4" role="tabpanel">
                            <div class="row g-4">
                                <div class="col-md-5">
                                    <div class="card border border-light-subtle shadow-none p-3 h-100" style="border-radius: 0;">
                                        <h6 class="fw-bold text-dark mb-2"><i class="fa fa-image text-primary me-2"></i> Main Thumbnail Image <span class="text-danger">*</span></h6>
                                        <p class="small text-muted mb-3">Primary cover image displayed across catalog and search listings.</p>

                                        <div class="text-center p-4 border border-dashed bg-light mb-3">
                                            <div id="wizardThumbPlaceholder">
                                                <i class="fa fa-cloud-arrow-up text-secondary fs-1 mb-2"></i>
                                                <div class="small fw-medium text-muted">Upload cover image</div>
                                            </div>
                                            <img id="wizardThumbPreview" src="" class="img-fluid border shadow-sm" style="max-height: 180px; display: none;">
                                        </div>

                                        <input type="file" name="thumbnail" id="wizardThumbInput" class="form-control" accept="image/*" onchange="previewWizardThumb(this)">
                                    </div>
                                </div>

                                <div class="col-md-7">
                                    <div class="card border border-light-subtle shadow-none p-3 h-100" style="border-radius: 0;">
                                        <h6 class="fw-bold text-dark mb-2"><i class="fa fa-images text-info me-2"></i> Additional Gallery Images</h6>
                                        <p class="small text-muted mb-3">Upload multiple photos showing different angles, packaging, or colors.</p>

                                        <div class="p-4 border border-dashed bg-light mb-3 text-center">
                                            <i class="fa fa-photo-film text-secondary fs-1 mb-2"></i>
                                            <div class="small fw-medium text-muted mb-2">Select multiple gallery images (JPG, PNG, WebP)</div>
                                            <input type="file" name="gallery[]" id="wizardGalleryInput" class="form-control" accept="image/*" multiple onchange="previewWizardGallery(this)">
                                        </div>

                                        <div id="wizardGalleryPreviewContainer" class="d-flex flex-wrap gap-2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ==================== STEP 5: SEO & PUBLISHING ==================== --}}
                        <div class="tab-pane fade" id="step5" role="tabpanel">
                            <div class="row g-4">
                                <div class="col-lg-7">
                                    <div class="card border border-light-subtle shadow-none p-3 mb-4" style="border-radius: 0;">
                                        <h6 class="fw-bold text-dark mb-3"><i class="fa fa-magnifying-glass text-secondary me-2"></i> SEO & Search Metadata</h6>
                                        
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Search Meta Title</label>
                                            <input type="text" name="meta_title" id="seoTitleInput" class="form-control" value="{{ old('meta_title') }}" placeholder="e.g. Buy Premium Wireless Headphones Online at Best Price">
                                        </div>

                                        <div class="mb-0">
                                            <label class="form-label fw-bold">Search Meta Description</label>
                                            <textarea name="meta_description" id="seoDescInput" rows="3" class="form-control" placeholder="Meta description for search engine preview snippet...">{{ old('meta_description') }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-5">
                                    <div class="card border border-light-subtle shadow-none p-3 mb-4 bg-light" style="border-radius: 0;">
                                        <h6 class="fw-bold text-dark mb-3"><i class="fa fa-rocket text-primary me-2"></i> Publishing Status & Flags</h6>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Publishing Status</label>
                                            <select name="status" class="form-select fw-bold" required>
                                                <option value="published" {{ old('status', 'published') == 'published' ? 'selected' : '' }}>🟢 Published (Visible to Public)</option>
                                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>🟡 Draft (Hidden / Unpublished)</option>
                                                <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>⚪ Archived</option>
                                            </select>
                                        </div>

                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" name="is_featured" id="wizardIsFeatured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="wizardIsFeatured">Feature on Homepage</label>
                                        </div>

                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" name="is_refundable" id="wizardIsRefundable" value="1" {{ old('is_refundable', 1) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="wizardIsRefundable">Return / Refund Eligible</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Wizard Bottom Action Bar (Prev / Next / Finish) --}}
                <div class="card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-outline-secondary px-4" id="prevStepBtn" onclick="navigateWizard(-1)" style="display: none;">
                        <i class="fa fa-chevron-left me-1"></i> Previous
                    </button>
                    <div></div> {{-- Spacer --}}
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-primary px-4 fw-bold shadow-sm" id="nextStepBtn" onclick="navigateWizard(1)">
                            Next Step <i class="fa fa-chevron-right ms-1"></i>
                        </button>
                        <button type="submit" class="btn btn-success px-4 py-2 fw-bold shadow-sm" id="finishStepBtn" style="display: none;">
                            <i class="fa fa-check-circle me-1"></i> Save & Publish Product
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('styles')
    <style>
        .wizard-steps .nav-link {
            border-radius: 0;
            border-bottom: 3px solid transparent;
            color: #64748b;
            background: #fafafa;
            transition: all 0.25s ease;
        }
        .wizard-steps .nav-link.active {
            color: #0d6efd;
            background: #ffffff;
            border-bottom-color: #0d6efd;
        }
        .wizard-steps .step-num {
            width: 28px;
            height: 28px;
            line-height: 28px;
            border-radius: 50%;
            background: #e2e8f0;
            color: #475569;
            font-weight: bold;
            font-size: 0.85rem;
            display: inline-block;
            text-align: center;
        }
        .wizard-steps .nav-link.active .step-num {
            background: #0d6efd;
            color: #ffffff;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        let currentStepIndex = 1;
        const totalSteps = 5;

        function updateWizardUI() {
            // Update Previous Button
            if (currentStepIndex === 1) {
                $('#prevStepBtn').hide();
            } else {
                $('#prevStepBtn').show();
            }

            // Update Next vs Finish Button
            if (currentStepIndex === totalSteps) {
                $('#nextStepBtn').hide();
                $('#finishStepBtn').show();
            } else {
                $('#nextStepBtn').show();
                $('#finishStepBtn').hide();
            }

            // Activate tab
            const tabTrigger = new bootstrap.Tab(document.querySelector(`#step${currentStepIndex}-tab`));
            tabTrigger.show();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function navigateWizard(direction) {
            if (direction === 1) {
                // Validation before advancing
                if (currentStepIndex === 1) {
                    const name = $('#productNameInput').val().trim();
                    if (!name) {
                        Swal.fire('Required Field', 'Please enter the Product Title to continue.', 'warning');
                        $('#productNameInput').focus();
                        return;
                    }
                }
                if (currentStepIndex === 2) {
                    const price = $('#basePriceInput').val();
                    if (!price || parseFloat(price) < 0) {
                        Swal.fire('Required Field', 'Please enter a valid selling price.', 'warning');
                        $('#basePriceInput').focus();
                        return;
                    }
                }
            }

            currentStepIndex += direction;
            if (currentStepIndex < 1) currentStepIndex = 1;
            if (currentStepIndex > totalSteps) currentStepIndex = totalSteps;

            updateWizardUI();
        }

        // Tab click synchronization
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            const targetId = $(e.target).attr('data-bs-target');
            currentStepIndex = parseInt(targetId.replace('#step', ''));
            updateWizardUI();
        });

        // Stock Toggle
        $('#manageStockToggle').on('change', function() {
            if ($(this).is(':checked')) {
                $('#stockFields').slideDown();
            } else {
                $('#stockFields').slideUp();
            }
        });

        // Product Type Change
        $('#productTypeSelect').on('change', function() {
            syncProductTypeUI($(this).val());
        });

        function syncProductTypeUI(type) {
            if (type === 'variable') {
                $('#simpleProductNotice').hide();
                $('#variableProductSection').show();
                if ($('#wizardVariantRows tr').length === 0) {
                    addWizardVariantRow('Option 1', 10);
                }
            } else {
                $('#simpleProductNotice').show();
                $('#variableProductSection').hide();
            }
        }

        function enableVariableMode() {
            $('#productTypeSelect').val('variable').trigger('change');
        }

        let wizardVarIdx = 0;
        function addWizardVariantRow(name = '', stock = 10) {
            wizardVarIdx++;
            const basePrice = $('#basePriceInput').val() || '';
            const rowHtml = `
                <tr id="wizardVarRow_${wizardVarIdx}">
                    <td>
                        <input type="text" name="variants[${wizardVarIdx}][name]" class="form-control form-control-sm fw-medium" value="${name}" placeholder="e.g. Red / 64GB" required>
                    </td>
                    <td>
                        <input type="text" name="variants[${wizardVarIdx}][sku]" class="form-control form-control-sm font-monospace" placeholder="Variant SKU">
                    </td>
                    <td>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0" name="variants[${wizardVarIdx}][price]" class="form-control" value="${basePrice}" placeholder="Price">
                        </div>
                    </td>
                    <td>
                        <input type="number" min="0" name="variants[${wizardVarIdx}][stock_quantity]" class="form-control form-control-sm fw-bold" value="${stock}" required>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="$('#wizardVarRow_${wizardVarIdx}').remove()" title="Remove Variant"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
            `;
            $('#wizardVariantRows').append(rowHtml);
        }

        // Image Previews
        function previewWizardThumb(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#wizardThumbPlaceholder').hide();
                    $('#wizardThumbPreview').attr('src', e.target.result).show();
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function previewWizardGallery(input) {
            $('#wizardGalleryPreviewContainer').empty();
            if (input.files) {
                Array.from(input.files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = $('<img>').attr('src', e.target.result).addClass('border shadow-sm').css({ width: '60px', height: '60px', objectFit: 'cover' });
                        $('#wizardGalleryPreviewContainer').append(img);
                    }
                    reader.readAsDataURL(file);
                });
            }
        }

        // Initialize state
        syncProductTypeUI($('#productTypeSelect').val());
    </script>
    @endpush
</x-admin-layout>
