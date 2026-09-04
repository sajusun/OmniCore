<?php

namespace App\Modules\Product\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Modules\Product\Models\ProductBrand;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ProductBrand::withCount('products')->latest();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('status', fn ($row) => $row->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>')
                ->addColumn('website_link', fn ($row) => $row->website ? '<a href="' . $row->website . '" target="_blank" class="text-primary">' . $row->website . '</a>' : '<span class="text-muted small">N/A</span>')
                ->addColumn('action', function ($row) {
                    return '<div class="btn-group btn-group-sm">
                                <a href="' . url('api/v1/store/brands/' . $row->slug) . '" target="_blank" class="btn btn-outline-info"><i class="fa fa-eye"></i></a>
                            </div>';
                })
                ->rawColumns(['status', 'website_link', 'action'])
                ->make(true);
        }

        return view('product::backend.brands.index');
    }
}
