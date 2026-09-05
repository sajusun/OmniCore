<?php

namespace App\Modules\Order\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Order\Http\Resources\OrderResource;
use App\Modules\Order\Models\Order;
use App\Modules\Order\Services\OrderService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckoutOrderController extends Controller
{
    public function __construct(protected OrderService $orderService)
    {
        parent::__construct();
    }

    /**
     * Place order from active cart (Atomic Checkout)
     */
    public function checkout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'address_id'                      => 'nullable|integer|exists:user_addresses,id',
            'shipping_address'                => 'required_without:address_id|array',
            'shipping_address.recipient_name' => 'required_with:shipping_address|string|max:255',
            'shipping_address.phone'          => 'required_with:shipping_address|string|max:30',
            'shipping_address.street_address' => 'required_with:shipping_address|string|max:255',
            'shipping_address.city'           => 'required_with:shipping_address|string|max:100',
            'shipping_address.postal_code'    => 'required_with:shipping_address|string|max:20',
            'shipping_address.country'        => 'nullable|string|max:100',
            'billing_address'                 => 'nullable|array',
            'shipping_method_id'              => 'nullable|integer|exists:shipping_methods,id',
            'payment_method'                  => 'required|in:cod,stripe,sslcommerz,bkash,bank_transfer',
            'customer_notes'                  => 'nullable|string|max:1000',
        ]);

        $user = $request->user();

        try {
            $order = $this->orderService->checkout($user, $validated);

            return $this->created(
                new OrderResource($order),
                'Order placed successfully!'
            );
        } catch (Exception $e) {
            return $this->error($e->getMessage(), null, 422);
        }
    }

    /**
     * Customer's past orders
     */
    public function index(Request $request): JsonResponse
    {
        $orders = $request->user()->orders()
            ->with(['items.product.media', 'shippingMethod'])
            ->latest()
            ->paginate((int) $request->get('per_page', 10));

        return $this->paginated(
            $orders,
            OrderResource::class,
            'Orders fetched successfully.'
        );
    }

    /**
     * Show single order details
     */
    public function show(string $orderNumber, Request $request): JsonResponse
    {
        $order = Order::with(['items.product.media', 'items.variant', 'shippingMethod', 'histories'])
            ->where('order_number', $orderNumber)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return $this->success(
            new OrderResource($order),
            'Order details fetched successfully.'
        );
    }

    /**
     * Track order status and timeline
     */
    public function track(string $orderNumber, Request $request): JsonResponse
    {
        $order = Order::with(['histories', 'shippingMethod'])
            ->where('order_number', $orderNumber)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return $this->success([
            'order_number'   => $order->order_number,
            'current_status' => $order->status,
            'payment_status' => $order->payment_status,
            'created_at'     => $order->created_at->toIso8601String(),
            'timeline'       => $order->histories->map(fn ($h) => [
                'status'  => $h->status,
                'comment' => $h->comment,
                'time'    => $h->created_at->toIso8601String(),
            ]),
        ], 'Order tracking status fetched.');
    }

    /**
     * Cancel order (if still pending/confirmed)
     */
    public function cancel(string $orderNumber, Request $request): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        try {
            $updatedOrder = $this->orderService->cancelOrder($order, $request->user(), $request->reason);

            return $this->success(
                new OrderResource($updatedOrder),
                'Order cancelled successfully.'
            );
        } catch (Exception $e) {
            return $this->error($e->getMessage(), null, 422);
        }
    }
}
