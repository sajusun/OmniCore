<?php

namespace App\Modules\Order\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Order\Http\Resources\OrderResource;
use App\Modules\Order\Models\Order;
use App\Modules\Order\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function __construct(protected OrderService $orderService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $orders = $this->orderService->getAdminOrders(
            $request->all(),
            (int) $request->get('per_page', 20)
        );

        return response()->json([
            'success' => true,
            'data' => OrderResource::collection($orders),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $order = Order::with(['user', 'items.product.media', 'items.variant', 'shippingMethod', 'histories.user'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new OrderResource($order),
        ]);
    }

    public function updateStatus(int $id, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,out_for_delivery,delivered,cancelled,refunded',
            'comment' => 'nullable|string|max:500',
        ]);

        $order = Order::findOrFail($id);
        $updatedOrder = $this->orderService->updateOrderStatus(
            $order,
            $validated['status'],
            $validated['comment'] ?? null,
            $request->user()->id
        );

        return response()->json([
            'success' => true,
            'message' => "Order status updated to {$validated['status']}.",
            'data' => new OrderResource($updatedOrder),
        ]);
    }

    public function updatePaymentStatus(int $id, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'payment_status' => 'required|in:unpaid,paid,refunded,failed',
            'transaction_id' => 'nullable|string|max:100',
        ]);

        $order = Order::findOrFail($id);
        $order->update([
            'payment_status' => $validated['payment_status'],
            'transaction_id' => $validated['transaction_id'] ?? $order->transaction_id,
            'paid_at' => $validated['payment_status'] === 'paid' ? now() : $order->paid_at,
        ]);

        $order->addHistory(
            $order->status,
            "Payment status changed to {$validated['payment_status']}.",
            $request->user()->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Payment status updated.',
            'data' => new OrderResource($order->fresh(['user', 'items', 'histories'])),
        ]);
    }
}
