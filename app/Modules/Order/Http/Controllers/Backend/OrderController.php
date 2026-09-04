<?php

namespace App\Modules\Order\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Modules\Order\Models\Order;
use App\Modules\Order\Services\OrderService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService)
    {
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Order::with(['user', 'items'])->latest();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('customer', function ($row) {
                    if ($row->user) {
                        return '<div class="fw-bold">' . $row->user->name . '</div><small class="text-muted">' . $row->user->email . '</small>';
                    }
                    $name = $row->shipping_address['recipient_name'] ?? 'Guest';
                    return '<div class="fw-bold">' . $name . '</div><small class="text-muted">Guest Checkout</small>';
                })
                ->addColumn('items_count', fn ($row) => $row->items->sum('quantity') . ' items')
                ->editColumn('total_amount', fn ($row) => '<span class="fw-bold text-success">$' . number_format($row->total_amount, 2) . '</span>')
                ->editColumn('status', function ($row) {
                    $badge = match ($row->status) {
                        'delivered' => 'bg-success',
                        'shipped', 'out_for_delivery' => 'bg-info',
                        'confirmed', 'processing' => 'bg-primary',
                        'pending' => 'bg-warning text-dark',
                        'cancelled', 'refunded' => 'bg-danger',
                        default => 'bg-secondary'
                    };
                    return '<span class="badge ' . $badge . '">' . ucfirst(str_replace('_', ' ', $row->status)) . '</span>';
                })
                ->editColumn('payment_status', function ($row) {
                    $badge = $row->payment_status === 'paid' ? 'bg-success' : ($row->payment_status === 'failed' ? 'bg-danger' : 'bg-secondary');
                    return '<span class="badge ' . $badge . '">' . ucfirst($row->payment_status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    return '<div class="btn-group btn-group-sm">
                                <a href="' . url('api/v1/admin/orders/' . $row->id) . '" target="_blank" class="btn btn-outline-info" title="View Order Details"><i class="fa fa-eye"></i></a>
                            </div>';
                })
                ->rawColumns(['customer', 'total_amount', 'status', 'payment_status', 'action'])
                ->make(true);
        }

        return view('order::backend.orders.index');
    }
}
