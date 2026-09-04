<?php

namespace App\Modules\Product\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Modules\Product\Models\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Product::with(['category', 'brand'])
                ->where('manage_stock', true)
                ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
                ->orderBy('stock_quantity', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('name_info', function ($row) {
                    $thumb = $row->thumbnail_url ?? asset('default/product.png');
                    return '<div class="d-flex align-items-center gap-2">
                                <img src="' . $thumb . '" class="rounded border" style="width: 40px; height: 40px; object-fit: cover;" onError="this.src=\'https://placehold.co/100x100?text=Product\';">
                                <div>
                                    <div class="fw-bold">' . e($row->name) . '</div>
                                    <small class="text-muted">SKU: <code>' . e($row->sku ?? 'N/A') . '</code></small>
                                </div>
                            </div>';
                })
                ->addColumn('category', fn ($row) => $row->category ? '<span class="badge bg-light text-dark border">' . e($row->category->name) . '</span>' : '<span class="text-muted small">None</span>')
                ->addColumn('current_stock', function ($row) {
                    if ($row->stock_quantity <= 0) {
                        return '<span class="badge bg-danger">OUT OF STOCK (0)</span>';
                    }
                    return '<span class="badge bg-warning text-dark font-monospace fs-6">' . $row->stock_quantity . '</span>';
                })
                ->addColumn('threshold', fn ($row) => '<span class="badge bg-secondary">' . $row->low_stock_threshold . '</span>')
                ->addColumn('action', function ($row) {
                    return '<div class="d-flex align-items-center gap-1">
                                <button type="button" class="btn btn-sm btn-success" onclick="openRestockModal(' . $row->id . ', \'' . addslashes($row->name) . '\', ' . $row->stock_quantity . ')" title="Adjust Stock"><i class="fa fa-plus me-1"></i> Restock</button>
                                <a href="' . route('admin.products.edit', $row->id) . '" class="btn btn-sm btn-outline-primary" title="Edit Product"><i class="fa fa-pencil"></i></a>
                            </div>';
                })
                ->rawColumns(['name_info', 'category', 'current_stock', 'threshold', 'action'])
                ->make(true);
        }

        return view('product::backend.inventory.index');
    }

    public function updateStock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'stock_quantity' => 'required|integer|min:0',
        ]);

        $product = Product::findOrFail($request->product_id);
        $product->stock_quantity = $request->stock_quantity;
        $product->is_in_stock = $product->stock_quantity > 0;
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Stock updated successfully for ' . $product->name,
        ]);
    }
}
