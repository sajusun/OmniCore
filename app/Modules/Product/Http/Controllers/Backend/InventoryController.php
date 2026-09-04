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
                ->addColumn('name', fn ($row) => '<span class="fw-bold">' . $row->name . '</span><br><small class="text-muted">' . ($row->sku ?? 'No SKU') . '</small>')
                ->addColumn('category', fn ($row) => $row->category ? $row->category->name : 'N/A')
                ->addColumn('stock_status', function ($row) {
                    if ($row->stock_quantity <= 0) {
                        return '<span class="badge bg-danger">OUT OF STOCK (0)</span>';
                    }
                    return '<span class="badge bg-warning text-dark">' . $row->stock_quantity . ' left (Threshold: ' . $row->low_stock_threshold . ')</span>';
                })
                ->rawColumns(['name', 'stock_status'])
                ->make(true);
        }

        return view('product::backend.inventory.index');
    }
}
