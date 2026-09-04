<?php

namespace App\Modules\Product\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Modules\Product\Models\ProductCategory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ProductCategory::with('parent')->withCount('products')->orderBy('order', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('parent', fn ($row) => $row->parent ? $row->parent->name : '<span class="text-muted small">Root Category</span>')
                ->addColumn('status', fn ($row) => $row->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>')
                ->addColumn('action', function ($row) {
                    return '<div class="btn-group btn-group-sm">
                                <a href="' . url('api/v1/store/categories/' . $row->slug) . '" target="_blank" class="btn btn-outline-info" title="Preview"><i class="fa fa-eye"></i></a>
                            </div>';
                })
                ->rawColumns(['parent', 'status', 'action'])
                ->make(true);
        }

        return view('product::backend.categories.index');
    }
}
