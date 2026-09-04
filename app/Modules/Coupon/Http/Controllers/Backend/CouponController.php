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
                ->editColumn('code', fn ($row) => '<span class="badge bg-primary fs-6 font-monospace">' . e($row->code) . '</span>')
                ->addColumn('discount_display', function ($row) {
                    if ($row->type === 'percentage') {
                        $max = $row->max_discount_amount ? ' (Up to $' . number_format($row->max_discount_amount, 2) . ')' : '';
                        return '<strong class="text-success">' . $row->value . '% OFF</strong>' . $max;
                    }
                    if ($row->type === 'free_shipping') {
                        return '<strong class="text-info"><i class="fa fa-truck me-1"></i> Free Shipping</strong>';
                    }
                    return '<strong class="text-success">$' . number_format($row->value, 2) . ' FLAT OFF</strong>';
                })
                ->addColumn('min_spend', fn ($row) => $row->min_order_amount > 0 ? '$' . number_format($row->min_order_amount, 2) : '<span class="text-muted small">No minimum</span>')
                ->addColumn('usage', function ($row) {
                    $limit = $row->usage_limit ?: '∞';
                    return '<span class="badge bg-light text-dark border">' . $row->usage_count . ' / ' . $limit . '</span>';
                })
                ->addColumn('validity', function ($row) {
                    if ($row->expires_at) {
                        $isExpired = $row->expires_at->isPast();
                        $badge = $isExpired ? 'text-danger' : 'text-muted';
                        return '<small class="' . $badge . '">' . $row->expires_at->format('M d, Y') . '</small>';
                    }
                    return '<small class="text-muted">Never expires</small>';
                })
                ->addColumn('status', fn ($row) => $row->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Disabled</span>')
                ->addColumn('action', function ($row) {
                    $json = htmlspecialchars(json_encode([
                        'id' => $row->id,
                        'code' => $row->code,
                        'type' => $row->type,
                        'value' => (float)$row->value,
                        'min_order_amount' => (float)($row->min_order_amount ?? 0),
                        'max_discount_amount' => (float)($row->max_discount_amount ?? 0),
                        'usage_limit' => $row->usage_limit,
                        'usage_limit_per_user' => $row->usage_limit_per_user,
                        'starts_at' => $row->starts_at ? $row->starts_at->format('Y-m-d') : '',
                        'expires_at' => $row->expires_at ? $row->expires_at->format('Y-m-d') : '',
                        'is_active' => (bool)$row->is_active,
                        'description' => $row->description,
                    ]), ENT_QUOTES, 'UTF-8');

                    return '<div class="d-flex align-items-center gap-1">
                                <button type="button" class="btn btn-sm btn-primary" onclick=\'openEditCouponModal(' . $json . ')\' title="Edit"><i class="fa fa-pencil"></i></button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteCoupon(' . $row->id . ')" title="Delete"><i class="fa fa-trash"></i></button>
                            </div>';
                })
                ->rawColumns(['code', 'discount_display', 'min_spend', 'usage', 'validity', 'status', 'action'])
                ->make(true);
        }

        return view('coupon::backend.coupons.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'type' => 'required|in:percentage,fixed,free_shipping',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'nullable|boolean',
            'description' => 'nullable|string',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['is_active'] = $request->boolean('is_active', true);

        Coupon::create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Coupon created successfully!']);
        }

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created successfully!');
    }

    public function update(Request $request, int $id)
    {
        $coupon = Coupon::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'type' => 'required|in:percentage,fixed,free_shipping',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date',
            'is_active' => 'nullable|boolean',
            'description' => 'nullable|string',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['is_active'] = $request->boolean('is_active', true);

        $coupon->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Coupon updated successfully!']);
        }

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated successfully!');
    }

    public function destroy(int $id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        return response()->json([
            'success' => true,
            'message' => 'Coupon deleted successfully.',
        ]);
    }
}
