<?php

namespace App\Modules\Product\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Services\ProductService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    public function __construct(protected ProductService $productService)
    {
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Product::with(['category', 'brand', 'media'])->latest();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('thumbnail', function ($row) {
                    $thumb = $row->thumbnail_url ?? asset('default/product.png');
                    return '<img src="' . $thumb . '" class="rounded" style="width: 44px; height: 44px; object-fit: cover; border: 1px solid #e2e8f0;" onError="this.src=\'https://placehold.co/100x100?text=Product\';">';
                })
                ->addColumn('category', fn ($row) => $row->category ? '<span class="badge bg-light text-dark">' . $row->category->name . '</span>' : '<span class="text-muted small">None</span>')
                ->addColumn('brand', fn ($row) => $row->brand ? '<span class="fw-medium">' . $row->brand->name . '</span>' : '<span class="text-muted small">None</span>')
                ->editColumn('price', fn ($row) => '$' . number_format($row->price, 2))
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
                ->editColumn('type', fn ($row) => '<span class="badge bg-info">' . ucfirst($row->type) . '</span>')
                ->editColumn('status', function ($row) {
                    $badge = match ($row->status) {
                        'published' => 'bg-success',
                        'draft' => 'bg-secondary',
                        'archived' => 'bg-dark',
                        default => 'bg-light text-dark'
                    };
                    return '<span class="badge ' . $badge . '">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    return '<div class="btn-group btn-group-sm">
                                <a href="' . url('api/v1/store/products/' . $row->slug) . '" target="_blank" class="btn btn-outline-info" title="Preview API"><i class="fa fa-eye"></i></a>
                                <button type="button" class="btn btn-outline-danger" onclick="deleteProduct(' . $row->id . ')" title="Delete"><i class="fa fa-trash"></i></button>
                            </div>';
                })
                ->rawColumns(['thumbnail', 'category', 'brand', 'stock', 'type', 'status', 'action'])
                ->make(true);
        }

        return view('product::backend.products.index');
    }

    public function destroy(int $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.',
        ]);
    }
}
