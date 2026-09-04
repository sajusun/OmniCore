<x-admin-layout>
    <x-slot name="title">Product Details: {{ $product->name }}</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Product Details</h2>
    </x-slot>

    <div class="container-fluid py-2 px-0">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
            <div>
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb mb-0" style="font-size: 0.875rem;">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}" class="text-decoration-none text-muted">Products</a></li>
                        <li class="breadcrumb-item active text-dark fw-medium">Details</li>
                    </ol>
                </nav>
                <h4 class="fw-bold text-dark mb-0">{{ $product->name }}</h4>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary">
                    <i class="fa fa-pencil me-1"></i> Edit Product
                </a>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                    <i class="fa fa-arrow-left me-1"></i> Back to List
                </a>
            </div>
        </div>

        <div class="row g-4">
            {{-- Left Column: Details & Tabs --}}
            <div class="col-lg-8">
                {{-- Product Overview Card --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-4 text-center">
                                <img src="{{ $product->thumbnail_url ?? asset('default/product.png') }}" class="img-fluid border shadow-sm" style="max-height: 220px; object-fit: cover;" onError="this.src='https://placehold.co/300x300?text=Product';">
                                
                                @if($product->media && $product->media->where('collection_name', 'gallery')->isNotEmpty())
                                    <div class="mt-3 d-flex flex-wrap justify-content-center gap-2">
                                        @foreach($product->media->where('collection_name', 'gallery') as $med)
                                            <img src="{{ $med->url }}" class="border" style="width: 45px; height: 45px; object-fit: cover;">
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-8">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge bg-primary text-uppercase">{{ $product->type }}</span>
                                    @if($product->is_featured)
                                        <span class="badge bg-warning text-dark"><i class="fa fa-star me-1"></i> Featured</span>
                                    @endif
                                    <span class="badge {{ $product->status === 'published' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($product->status) }}</span>
                                </div>

                                <h3 class="fw-bold text-dark mb-1">{{ $product->name }}</h3>
                                <div class="text-muted small mb-3">SKU: <code>{{ $product->sku }}</code> | Created {{ $product->created_at->format('M d, Y') }}</div>

                                <div class="d-flex align-items-baseline gap-3 mb-3">
                                    <span class="fs-3 fw-bold text-success">${{ number_format($product->price, 2) }}</span>
                                    @if($product->compare_at_price && $product->compare_at_price > $product->price)
                                        <del class="text-muted fs-5">${{ number_format($product->compare_at_price, 2) }}</del>
                                        <span class="badge bg-danger">{{ $product->discount_percentage }}% OFF</span>
                                    @endif
                                </div>

                                <p class="text-secondary mb-3">{{ $product->short_description ?? 'No short description provided.' }}</p>

                                <div class="d-flex flex-wrap gap-2">
                                    @if($product->category)
                                        <span class="badge bg-light text-dark border"><i class="fa fa-folder me-1 text-primary"></i> {{ $product->category->name }}</span>
                                    @endif
                                    @if($product->brand)
                                        <span class="badge bg-light text-dark border"><i class="fa fa-tag me-1 text-info"></i> {{ $product->brand->name }}</span>
                                    @endif
                                    @foreach($product->tags as $tag)
                                        <span class="badge bg-secondary-subtle text-secondary">#{{ $tag->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Full Description --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold text-dark"><i class="fa fa-align-left me-2 text-primary"></i> Full Description</h5>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-dark mb-0" style="line-height: 1.7;">{!! nl2br(e($product->description ?? 'No full description available.')) !!}</p>
                    </div>
                </div>

                {{-- Variants Table --}}
                @if($product->variants->isNotEmpty())
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
                    <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-bold text-dark"><i class="fa fa-sliders me-2 text-info"></i> Product Variants ({{ $product->variants->count() }})</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Variant</th>
                                        <th>SKU</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product->variants as $var)
                                    <tr>
                                        <td class="ps-4 fw-bold">{{ $var->name }}</td>
                                        <td><code>{{ $var->sku }}</code></td>
                                        <td class="fw-bold text-success">${{ number_format($var->price, 2) }}</td>
                                        <td>
                                            @if($var->stock_quantity <= 0)
                                                <span class="badge bg-danger">Out of Stock</span>
                                            @else
                                                <span class="badge bg-success">{{ $var->stock_quantity }} Available</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $var->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $var->is_active ? 'Active' : 'Disabled' }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Customer Reviews --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
                    <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-bold text-dark"><i class="fa fa-star me-2 text-warning"></i> Customer Reviews ({{ $product->reviews->count() }})</h5>
                        <span class="badge bg-warning text-dark"><i class="fa fa-star me-1"></i> {{ $product->average_rating ?? '0.00' }} / 5.0</span>
                    </div>
                    <div class="card-body p-4">
                        @forelse($product->reviews as $rev)
                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="fw-bold">{{ $rev->user ? $rev->user->name : 'Anonymous Guest' }}</div>
                                    <div class="text-warning small">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fa{{ $i <= $rev->rating ? 's' : 'r' }} fa-star"></i>
                                        @endfor
                                    </div>
                                </div>
                                <p class="text-muted small mb-1">{{ $rev->comment }}</p>
                                <span class="badge {{ $rev->status === 'approved' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} small">{{ ucfirst($rev->status) }}</span>
                            </div>
                        @empty
                            <p class="text-muted text-center py-3 mb-0">No customer reviews yet for this product.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Right Column: Stats & Metadata --}}
            <div class="col-lg-4">
                {{-- Inventory Summary --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold text-dark"><i class="fa fa-boxes-stacked me-2 text-warning"></i> Stock & Inventory</h5>
                    </div>
                    <div class="card-body p-4">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>Track Stock</span>
                                <span class="badge {{ $product->manage_stock ? 'bg-success' : 'bg-secondary' }}">{{ $product->manage_stock ? 'Yes' : 'No' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>Total Available Quantity</span>
                                <span class="fw-bold fs-5 {{ $product->stock_quantity <= $product->low_stock_threshold ? 'text-danger' : 'text-success' }}">{{ $product->stock_quantity }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>Low Stock Threshold</span>
                                <span class="fw-bold">{{ $product->low_stock_threshold }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>Cost Price</span>
                                <span class="fw-bold text-muted">${{ number_format($product->cost_price, 2) }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Sales & Views Stats --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold text-dark"><i class="fa fa-chart-line me-2 text-info"></i> Performance</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row text-center g-3">
                            <div class="col-6">
                                <div class="p-3 bg-light">
                                    <div class="fs-4 fw-bold text-primary">{{ $product->sales_count ?? 0 }}</div>
                                    <small class="text-muted">Total Sales</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-light">
                                    <div class="fs-4 fw-bold text-info">{{ $product->views_count ?? 0 }}</div>
                                    <small class="text-muted">Page Views</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SEO Info --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold text-dark"><i class="fa fa-magnifying-glass me-2 text-secondary"></i> SEO Meta</h5>
                    </div>
                    <div class="card-body p-4">
                        <p class="small mb-2"><strong>Meta Title:</strong> {{ $product->meta_title ?? 'N/A' }}</p>
                        <p class="small mb-0"><strong>Meta Description:</strong> {{ $product->meta_description ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
