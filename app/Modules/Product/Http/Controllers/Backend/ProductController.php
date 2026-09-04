<?php

namespace App\Modules\Product\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Modules\Media\Services\MediaService;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\ProductAttribute;
use App\Modules\Product\Models\ProductBrand;
use App\Modules\Product\Models\ProductCategory;
use App\Modules\Product\Models\ProductTag;
use App\Modules\Product\Models\ProductVariant;
use App\Modules\Product\Services\ProductService;
use App\Modules\Product\Services\VariantMatrixService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService,
        protected VariantMatrixService $variantMatrixService,
        protected MediaService $mediaService
    ) {
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Product::with(['category', 'brand', 'media'])->latest();

            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }
            if ($request->filled('brand_id')) {
                $query->where('brand_id', $request->brand_id);
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('thumbnail', function ($row) {
                    $thumb = $row->thumbnail_url ?? asset('default/product.png');
                    return '<img src="' . $thumb . '" class="rounded shadow-sm" style="width: 46px; height: 46px; object-fit: cover; border: 1px solid #e2e8f0;" onError="this.src=\'https://placehold.co/100x100?text=Product\';">';
                })
                ->addColumn('name_info', function ($row) {
                    $badge = $row->is_featured ? ' <span class="badge bg-warning text-dark"><i class="fa fa-star"></i> Featured</span>' : '';
                    return '<div>
                                <a href="' . route('admin.products.show', $row->id) . '" class="fw-bold text-dark text-decoration-none">' . e($row->name) . '</a>' . $badge . '
                                <div class="text-muted small">SKU: <code>' . e($row->sku ?? 'N/A') . '</code></div>
                            </div>';
                })
                ->addColumn('category', fn ($row) => $row->category ? '<span class="badge bg-light text-dark border">' . e($row->category->name) . '</span>' : '<span class="text-muted small">None</span>')
                ->addColumn('brand', fn ($row) => $row->brand ? '<span class="fw-medium text-secondary">' . e($row->brand->name) . '</span>' : '<span class="text-muted small">None</span>')
                ->addColumn('price_display', function ($row) {
                    $html = '<span class="fw-bold text-success">$' . number_format($row->price, 2) . '</span>';
                    if ($row->compare_at_price && $row->compare_at_price > $row->price) {
                        $html .= '<br><del class="text-muted small">$' . number_format($row->compare_at_price, 2) . '</del>';
                    }
                    return $html;
                })
                ->addColumn('stock', function ($row) {
                    if (! $row->manage_stock) {
                        return '<span class="badge bg-secondary">Not Tracked</span>';
                    }
                    if ($row->stock_quantity <= 0) {
                        return '<span class="badge bg-danger">Out of Stock</span>';
                    }
                    if ($row->stock_quantity <= $row->low_stock_threshold) {
                        return '<span class="badge bg-warning text-dark">' . $row->stock_quantity . ' (Low)</span>';
                    }
                    return '<span class="badge bg-success">' . $row->stock_quantity . ' In Stock</span>';
                })
                ->editColumn('type', fn ($row) => '<span class="badge bg-info text-capitalize">' . e($row->type) . '</span>')
                ->editColumn('status', function ($row) {
                    $badge = match ($row->status) {
                        'published' => 'bg-success',
                        'draft' => 'bg-secondary',
                        'archived' => 'bg-dark',
                        default => 'bg-light text-dark'
                    };
                    return '<span class="badge ' . $badge . ' text-capitalize">' . e($row->status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $showUrl = route('admin.products.show', $row->id);
                    $editUrl = route('admin.products.edit', $row->id);
                    return '<div class="d-flex align-items-center gap-1">
                                <a href="' . $showUrl . '" class="btn btn-sm btn-info text-white" title="View Product"><i class="fa fa-eye"></i></a>
                                <a href="' . $editUrl . '" class="btn btn-sm btn-primary" title="Edit Product"><i class="fa fa-pencil"></i></a>
                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteProduct(' . $row->id . ')" title="Delete"><i class="fa fa-trash"></i></button>
                            </div>';
                })
                ->rawColumns(['thumbnail', 'name_info', 'category', 'brand', 'price_display', 'stock', 'type', 'status', 'action'])
                ->make(true);
        }

        $categories = ProductCategory::whereNull('parent_id')->with('children')->get();
        $brands = ProductBrand::orderBy('name')->get();

        return view('product::backend.products.index', compact('categories', 'brands'));
    }

    public function create()
    {
        $categories = ProductCategory::with('children')->get();
        $brands = ProductBrand::where('is_active', true)->orderBy('name')->get();
        $attributes = ProductAttribute::with('values')->get();
        $tags = ProductTag::orderBy('name')->get();

        return view('product::backend.products.create', compact('categories', 'brands', 'attributes', 'tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:product_categories,id',
            'brand_id' => 'nullable|exists:product_brands,id',
            'type' => 'required|in:simple,variable,digital',
            'price' => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'manage_stock' => 'nullable|boolean',
            'stock_quantity' => 'nullable|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'nullable|boolean',
            'is_refundable' => 'nullable|boolean',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'thumbnail' => 'nullable|image|max:4096',
            'gallery.*' => 'nullable|image|max:4096',
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(5);
        $validated['sku'] = $validated['sku'] ?: strtoupper(Str::slug(substr($validated['name'], 0, 3))) . '-' . rand(1000, 9999);
        $validated['manage_stock'] = $request->boolean('manage_stock');
        $validated['stock_quantity'] = $validated['manage_stock'] ? ($request->input('stock_quantity') ?? 0) : 0;
        $validated['low_stock_threshold'] = $request->input('low_stock_threshold') ?? 5;
        $validated['is_in_stock'] = ! $validated['manage_stock'] || $validated['stock_quantity'] > 0;
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_refundable'] = $request->boolean('is_refundable', true);

        $product = Product::create($validated);

        // Upload Thumbnail
        if ($request->hasFile('thumbnail')) {
            $this->mediaService->uploadThumbnail($product, $request->file('thumbnail'));
        }

        // Upload Gallery
        if ($request->hasFile('gallery')) {
            $this->mediaService->uploadGallery($product, $request->file('gallery'));
        }

        // Tags
        if ($request->filled('tags')) {
            $tagIds = [];
            $rawTags = is_array($request->tags) ? $request->tags : explode(',', $request->tags);
            foreach ($rawTags as $tagName) {
                $tagName = trim($tagName);
                if (! empty($tagName)) {
                    $tag = ProductTag::firstOrCreate(['name' => $tagName], ['slug' => Str::slug($tagName)]);
                    $tagIds[] = $tag->id;
                }
            }
            $product->tags()->sync($tagIds);
        }

        // Generate Variants if Variable
        if ($validated['type'] === 'variable' && $request->filled('variants')) {
            foreach ($request->variants as $varData) {
                if (! empty($varData['name'])) {
                    $product->variants()->create([
                        'name' => $varData['name'],
                        'sku' => $varData['sku'] ?? ($product->sku . '-' . Str::random(4)),
                        'price' => $varData['price'] ?? $product->price,
                        'compare_at_price' => $varData['compare_at_price'] ?? $product->compare_at_price,
                        'cost_price' => $varData['cost_price'] ?? $product->cost_price,
                        'stock_quantity' => $varData['stock_quantity'] ?? 10,
                        'is_in_stock' => ($varData['stock_quantity'] ?? 10) > 0,
                        'is_active' => true,
                        'combination_key' => $varData['combination_key'] ?? Str::slug($varData['name']),
                        'attributes_summary' => $varData['attributes'] ?? [],
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully!');
    }

    public function show(int $id)
    {
        $product = Product::with(['category', 'brand', 'variants', 'tags', 'media', 'reviews.user'])->findOrFail($id);
        return view('product::backend.products.show', compact('product'));
    }

    public function edit(int $id)
    {
        $product = Product::with(['category', 'brand', 'variants', 'tags', 'media'])->findOrFail($id);
        $categories = ProductCategory::with('children')->get();
        $brands = ProductBrand::where('is_active', true)->orderBy('name')->get();
        $attributes = ProductAttribute::with('values')->get();
        $tags = ProductTag::orderBy('name')->get();

        return view('product::backend.products.edit', compact('product', 'categories', 'brands', 'attributes', 'tags'));
    }

    public function update(Request $request, int $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:product_categories,id',
            'brand_id' => 'nullable|exists:product_brands,id',
            'type' => 'required|in:simple,variable,digital',
            'price' => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'manage_stock' => 'nullable|boolean',
            'stock_quantity' => 'nullable|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'nullable|boolean',
            'is_refundable' => 'nullable|boolean',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'thumbnail' => 'nullable|image|max:4096',
            'gallery.*' => 'nullable|image|max:4096',
        ]);

        $validated['manage_stock'] = $request->boolean('manage_stock');
        $validated['stock_quantity'] = $validated['manage_stock'] ? ($request->input('stock_quantity') ?? 0) : 0;
        $validated['low_stock_threshold'] = $request->input('low_stock_threshold') ?? 5;
        $validated['is_in_stock'] = ! $validated['manage_stock'] || $validated['stock_quantity'] > 0;
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_refundable'] = $request->boolean('is_refundable', true);

        $product->update($validated);

        if ($request->hasFile('thumbnail')) {
            $this->mediaService->uploadThumbnail($product, $request->file('thumbnail'));
        }

        if ($request->hasFile('gallery')) {
            $this->mediaService->uploadGallery($product, $request->file('gallery'));
        }

        // Tags
        if ($request->has('tags')) {
            $tagIds = [];
            $rawTags = is_array($request->tags) ? $request->tags : explode(',', $request->tags);
            foreach ($rawTags as $tagName) {
                $tagName = trim($tagName);
                if (! empty($tagName)) {
                    $tag = ProductTag::firstOrCreate(['name' => $tagName], ['slug' => Str::slug($tagName)]);
                    $tagIds[] = $tag->id;
                }
            }
            $product->tags()->sync($tagIds);
        }

        // Update Variants if passed
        if ($request->filled('existing_variants')) {
            foreach ($request->existing_variants as $varId => $vData) {
                $var = ProductVariant::where('product_id', $product->id)->find($varId);
                if ($var) {
                    $var->update([
                        'name' => $vData['name'] ?? $var->name,
                        'sku' => $vData['sku'] ?? $var->sku,
                        'price' => $vData['price'] ?? $var->price,
                        'stock_quantity' => $vData['stock_quantity'] ?? $var->stock_quantity,
                        'is_in_stock' => ($vData['stock_quantity'] ?? $var->stock_quantity) > 0,
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully!');
    }

    public function destroy(int $id)
    {
        $product = Product::findOrFail($id);
        $product->variants()->delete();
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.',
        ]);
    }
}
