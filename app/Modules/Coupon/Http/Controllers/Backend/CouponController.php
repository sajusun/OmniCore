<?php

namespace App\Modules\Coupon\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Modules\Coupon\Models\Coupon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Coupon::query()->latest();

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('code', fn ($row) => '<span class="badge bg-primary fs-6">' . $row->code . '</span>')
                ->addColumn('discount', function ($row) {
                    return $row->type === 'percentage'
                        ? $row->value . '% OFF'
                        : '$' . number_format($row->value, 2) . ' OFF';
                })
                ->addColumn('min_spend', fn ($row) => $row->min_order_amount > 0 ? '$' . number_format($row->min_order_amount, 2) : '<span class="text-muted small">No minimum</span>')
                ->addColumn('status', fn ($row) => $row->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Disabled</span>')
                ->rawColumns(['code', 'min_spend', 'status'])
                ->make(true);
        }

        return view('coupon::backend.coupons.index');
    }
}
