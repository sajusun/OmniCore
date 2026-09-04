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

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('payment_status')) {
                $query->where('payment_status', $request->payment_status);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('order_number_display', function ($row) {
                    return '<div>
                                <a href="' . route('admin.orders.show', $row->id) . '" class="fw-bold text-primary text-decoration-none">#' . e($row->order_number) . '</a>
                                <div class="text-muted small">' . $row->created_at->format('M d, Y - h:i A') . '</div>
                            </div>';
                })
                ->addColumn('customer', function ($row) {
                    if ($row->user) {
                        return '<div class="fw-bold">' . e($row->user->name) . '</div><small class="text-muted">' . e($row->user->email) . '</small>';
                    }
                    $name = $row->shipping_address['recipient_name'] ?? 'Guest';
                    return '<div class="fw-bold">' . e($name) . '</div><small class="text-muted">Guest Checkout</small>';
                })
                ->addColumn('items_count', fn ($row) => '<span class="badge bg-light text-dark border">' . $row->items->sum('quantity') . ' items</span>')
                ->editColumn('total_amount', fn ($row) => '<span class="fw-bold text-success fs-6">$' . number_format($row->total_amount, 2) . '</span>')
                ->editColumn('status', function ($row) {
                    $badge = match ($row->status) {
                        'delivered' => 'bg-success',
                        'shipped', 'out_for_delivery' => 'bg-info',
                        'confirmed', 'processing' => 'bg-primary',
                        'pending' => 'bg-warning text-dark',
                        'cancelled', 'refunded' => 'bg-danger',
                        default => 'bg-secondary'
                    };
                    return '<span class="badge ' . $badge . ' text-capitalize">' . ucfirst(str_replace('_', ' ', $row->status)) . '</span>';
                })
                ->editColumn('payment_status', function ($row) {
                    $badge = $row->payment_status === 'paid' ? 'bg-success' : ($row->payment_status === 'failed' ? 'bg-danger' : 'bg-warning text-dark');
                    return '<span class="badge ' . $badge . ' text-capitalize">' . ucfirst($row->payment_status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $showUrl = route('admin.orders.show', $row->id);
                    $invoiceUrl = route('admin.orders.invoice', $row->id);

                    return '<div class="d-flex align-items-center gap-1">
                                <a href="' . $showUrl . '" class="btn btn-sm btn-info text-white" title="View Order Details"><i class="fa fa-eye"></i></a>
                                <a href="' . $invoiceUrl . '" target="_blank" class="btn btn-sm btn-secondary" title="Print Invoice"><i class="fa fa-print"></i></a>
                            </div>';
                })
                ->rawColumns(['order_number_display', 'customer', 'items_count', 'total_amount', 'status', 'payment_status', 'action'])
                ->make(true);
        }

        return view('order::backend.orders.index');
    }

    public function show(int $id)
    {
        $order = Order::with(['user', 'items.product', 'items.variant', 'shippingMethod', 'histories.admin'])->findOrFail($id);
        return view('order::backend.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,out_for_delivery,delivered,cancelled,refunded',
            'notes' => 'nullable|string|max:500',
            'tracking_number' => 'nullable|string|max:100',
        ]);

        $order = Order::findOrFail($id);
        $this->orderService->updateStatus($order, $request->status, $request->notes, auth()->id());

        if ($request->filled('tracking_number')) {
            $order->tracking_number = $request->tracking_number;
            $order->save();
        }

        return redirect()->route('admin.orders.show', $order->id)->with('success', 'Order status updated to ' . ucfirst($request->status));
    }

    public function invoice(int $id)
    {
        $order = Order::with(['user', 'items.product', 'items.variant', 'shippingMethod'])->findOrFail($id);
        return view('order::backend.orders.invoice', compact('order'));
    }

    public function destroy(int $id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully.',
        ]);
    }
}
