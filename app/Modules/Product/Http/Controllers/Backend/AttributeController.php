<?php

namespace App\Modules\Product\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Modules\Product\Models\ProductAttribute;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AttributeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ProductAttribute::with('values')->latest();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('values', function ($row) {
                    return $row->values->map(function ($val) {
                        $style = $val->code ? 'border-left: 4px solid ' . $val->code . ';' : '';
                        return '<span class="badge bg-light text-dark me-1" style="' . $style . '">' . $val->value . '</span>';
                    })->implode(' ');
                })
                ->addColumn('type', fn ($row) => '<span class="badge bg-info">' . ucfirst($row->type) . '</span>')
                ->rawColumns(['values', 'type'])
                ->make(true);
        }

        return view('product::backend.attributes.index');
    }
}
