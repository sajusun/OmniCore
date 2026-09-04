<x-admin-layout>
    <x-slot name="title">Edit Product: {{ $product->name }}</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Product</h2>
    </x-slot>

    <div class="container-fluid py-2 px-0">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
            <div>
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb mb-0" style="font-size: 0.875rem;">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}" class="text-decoration-none text-muted">Products</a></li>
                        <li class="breadcrumb-item active text-dark fw-medium">Edit</li>
                    </ol>
                </nav>
                <h4 class="fw-bold text-dark mb-0">Edit Product: {{ $product->name }}</h4>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-info text-white">
                    <i class="fa fa-eye me-1"></i> View Details
                </a>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                    <i class="fa fa-arrow-left me-1"></i> Back
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

        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

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
                                <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Product Type <span class="text-danger">*</span></label>
                                    <select name="type" id="productType" class="form-select" required>
                                        <option value="simple" {{ old('type', $product->type) == 'simple' ? 'selected' : '' }}>Simple Product</option>
                                        <option value="variable" {{ old('type', $product->type) == 'variable' ? 'selected' : '' }}>Variable Product (Variants)</option>
                                        <option value="digital" {{ old('type', $product->type) == 'digital' ? 'selected' : '' }}>Digital / Downloadable</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">SKU (Stock Keeping Unit)</label>
                                    <input type="text" name="sku" class="form-control" value="{{ old('sku', $product->sku) }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Short Summary</label>
                                <textarea name="short_description" rows="2" class="form-control">{{ old('short_description', $product->short_description) }}</textarea>
                            </div>

                            <div class="mb-0">
                                <label class="form-label fw-bold">Detailed Description</label>
                                <textarea name="description" rows="6" class="form-control">{{ old('description', $product->description) }}</textarea>
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
                                        <input type="number" step="0.01" min="0" name="price" class="form-control" value="{{ old('price', $product->price) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Original / Compare Price ($)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" min="0" name="compare_at_price" class="form-control" value="{{ old('compare_at_price', $product->compare_at_price) }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Cost per Item ($)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" min="0" name="cost_price" class="form-control" value="{{ old('cost_price', $product->cost_price) }}">
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
                                <input class="form-check-input" type="checkbox" name="manage_stock" id="manageStock" value="1" {{ old('manage_stock', $product->manage_stock) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="manageStock">Track Stock Quantity for this Product</label>
                            </div>

                            <div class="row g-3" id="stockInputs" style="{{ $product->manage_stock ? '' : 'display: none;' }}">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Available Quantity</label>
                                    <input type="number" min="0" name="stock_quantity" class="form-control" value="{{ old('stock_quantity', $product->stock_quantity) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Low Stock Warning Threshold</label>
                                    <input type="number" min="0" name="low_stock_threshold" class="form-control" value="{{ old('low_stock_threshold', $product->low_stock_threshold) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Existing Variants Section --}}
                    @if($product->variants->isNotEmpty())
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
                        <div class="card-header bg-transparent border-bottom py-3">
                            <h5 class="card-title mb-0 fw-bold text-dark"><i class="fa fa-sliders me-2 text-info"></i> Product Variants ({{ $product->variants->count() }})</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Variant Title</th>
                                            <th>SKU</th>
                                            <th>Price ($)</th>
                                            <th>Stock Qty</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($product->variants as $variant)
                                        <tr>
                                            <td>
                                                <input type="text" name="existing_variants[{{ $variant->id }}][name]" class="form-control form-control-sm" value="{{ $variant->name }}" required>
                                            </td>
                                            <td>
                                                <input type="text" name="existing_variants[{{ $variant->id }}][sku]" class="form-control form-control-sm" value="{{ $variant->sku }}">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0" name="existing_variants[{{ $variant->id }}][price]" class="form-control form-control-sm" value="{{ $variant->price }}">
                                            </td>
                                            <td>
                                                <input type="number" min="0" name="existing_variants[{{ $variant->id }}][stock_quantity]" class="form-control form-control-sm" value="{{ $variant->stock_quantity }}">
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- 5. SEO Metadata --}}
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
                        <div class="card-header bg-transparent border-bottom py-3">
                            <h5 class="card-title mb-0 fw-bold text-dark"><i class="fa fa-magnifying-glass me-2 text-secondary"></i> Search Engine Optimization (SEO)</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Meta Title</label>
                                <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $product->meta_title) }}">
                            </div>
                            <div class="mb-0">
                                <label class="form-label fw-bold">Meta Description</label>
                                <textarea name="meta_description" rows="2" class="form-control">{{ old('meta_description', $product->meta_description) }}</textarea>
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
                                    <option value="published" {{ old('status', $product->status) == 'published' ? 'selected' : '' }}>Published (Active)</option>
                                    <option value="draft" {{ old('status', $product->status) == 'draft' ? 'selected' : '' }}>Draft (Hidden)</option>
                                    <option value="archived" {{ old('status', $product->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                                </select>
                            </div>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="is_featured" id="isFeatured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="isFeatured">Mark as Featured Product</label>
                            </div>

                            <div class="form-check form-switch mb-4">
                                <input class="form-check-input" type="checkbox" name="is_refundable" id="isRefundable" value="1" {{ old('is_refundable', $product->is_refundable) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="isRefundable">Eligible for Return / Refund</label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                                <i class="fa fa-save me-1"></i> Update Product
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
                                        <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                        @foreach($cat->children as $child)
                                            <option value="{{ $child->id }}" {{ old('category_id', $product->category_id) == $child->id ? 'selected' : '' }}>
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
                                        <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-0">
                                <label class="form-label fw-bold">Tags (Comma Separated)</label>
                                <input type="text" name="tags" class="form-control" value="{{ old('tags', $product->tags->pluck('name')->implode(', ')) }}">
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
                                <label class="form-label fw-bold">Main Product Thumbnail</label>
                                @if($product->thumbnail_url)
                                    <div class="mb-2">
                                        <img src="{{ $product->thumbnail_url }}" class="rounded border" style="max-height: 100px; object-fit: cover;">
                                    </div>
                                @endif
                                <input type="file" name="thumbnail" class="form-control" accept="image/*">
                            </div>

                            <div class="mb-0">
                                <label class="form-label fw-bold">Add Additional Gallery Images</label>
                                <input type="file" name="gallery[]" class="form-control" accept="image/*" multiple>
                                
                                @if($product->media && $product->media->where('collection_name', 'gallery')->isNotEmpty())
                                    <div class="mt-2 d-flex flex-wrap gap-2">
                                        @foreach($product->media->where('collection_name', 'gallery') as $med)
                                            <div class="position-relative border rounded p-1">
                                                <img src="{{ $med->url }}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        $('#manageStock').on('change', function() {
            if ($(this).is(':checked')) {
                $('#stockInputs').slideDown();
            } else {
                $('#stockInputs').slideUp();
            }
        });
    </script>
    @endpush
</x-admin-layout>
