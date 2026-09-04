<?php

namespace App\Modules\Order\Services;

use App\Models\User;
use App\Modules\Cart\Models\Cart;
use App\Modules\Cart\Services\CartService;
use App\Modules\Coupon\Models\Coupon;
use App\Modules\Coupon\Services\CouponService;
use App\Modules\Order\Models\Order;
use App\Modules\Order\Models\OrderItem;
use App\Modules\Order\Models\ShippingMethod;
use App\Modules\Order\Models\UserAddress;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\ProductVariant;
use App\Modules\Product\Services\InventoryService;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        protected CartService $cartService,
        protected InventoryService $inventoryService,
        protected CouponService $couponService
    ) {
    }

    /**
     * Atomic Checkout: Place order from user's active cart
     */
    public function checkout(User $user, array $data): Order
    {
        return DB::transaction(function () use ($user, $data) {
            // 1. Get user cart
            $cart = Cart::with(['items.product', 'items.variant.attributeValues.attribute'])
                ->where('user_id', $user->id)
                ->first();

            if (! $cart || $cart->items->isEmpty()) {
                throw new Exception('Your cart is empty. Please add items before checking out.');
            }

            // 2. Validate all item stock before placing order
            foreach ($cart->items as $item) {
                $product = $item->product;
                $variant = $item->variant;
                $stock = $variant ? $variant->stock_quantity : $product->stock_quantity;
                $manageStock = $variant ? $variant->manage_stock : $product->manage_stock;

                if ($manageStock && $stock < $item->quantity) {
                    $name = $variant ? "{$product->name} ({$variant->sku})" : $product->name;
                    throw new Exception("Insufficient stock for {$name}. Only {$stock} available.");
                }
            }

            // 3. Resolve Shipping Address
            if (isset($data['address_id'])) {
                $addressModel = UserAddress::where('user_id', $user->id)->findOrFail($data['address_id']);
                $shippingAddress = [
                    'recipient_name' => $addressModel->recipient_name,
                    'phone' => $addressModel->phone,
                    'street_address' => $addressModel->street_address,
                    'apartment_suite' => $addressModel->apartment_suite,
                    'city' => $addressModel->city,
                    'state' => $addressModel->state,
                    'postal_code' => $addressModel->postal_code,
                    'country' => $addressModel->country,
                ];
            } else {
                $shippingAddress = $data['shipping_address'];
            }

            // 4. Resolve Shipping Method & Fee
            $shippingMethodId = $data['shipping_method_id'] ?? null;
            $shippingFee = 0.00;
            if ($shippingMethodId) {
                $shippingMethod = ShippingMethod::find($shippingMethodId);
                if ($shippingMethod && $shippingMethod->is_active) {
                    $shippingFee = $shippingMethod->calculateCost($cart->subtotal);
                }
            }

            // 5. Calculate Totals
            $subtotal = $cart->subtotal;
            $discountAmount = (float) $cart->discount_amount;
            $taxAmount = round($subtotal * 0.05, 2); // 5% estimated tax or 0
            $totalAmount = max(0.00, round($subtotal - $discountAmount + $shippingFee + $taxAmount, 2));

            // 6. Generate Unique Order Number e.g. ORD-2026-9281
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(5));

            // 7. Create Order Record
            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $user->id,
                'status' => 'pending',
                'payment_status' => $data['payment_method'] === 'cod' ? 'unpaid' : 'unpaid',
                'payment_method' => $data['payment_method'] ?? 'cod',
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'coupon_code' => $cart->coupon_code,
                'shipping_fee' => $shippingFee,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'shipping_method_id' => $shippingMethodId,
                'shipping_address' => $shippingAddress,
                'billing_address' => $data['billing_address'] ?? $shippingAddress,
                'customer_notes' => $data['customer_notes'] ?? null,
            ]);

            // 8. Create Order Items & Decrement Stock
            foreach ($cart->items as $item) {
                $variantAttributes = null;
                if ($item->variant && $item->variant->attributeValues) {
                    $variantAttributes = $item->variant->attributeValues->map(fn ($v) => [
                        'name' => $v->attribute?->name,
                        'value' => $v->value,
                    ])->toArray();
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'product_name' => $item->product->name,
                    'variant_sku' => $item->variant?->sku,
                    'variant_attributes' => $variantAttributes,
                    'unit_price' => $item->unit_price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->subtotal,
                ]);

                // Decrement Stock & Increment Sales Count
                $this->inventoryService->adjustStock(
                    $item->product_id,
                    $item->variant_id,
                    $item->quantity,
                    'decrement'
                );

                $item->product->increment('sales_count', $item->quantity);
            }

            // 9. Record Coupon Usage if applied
            if ($cart->coupon_code) {
                $coupon = Coupon::where('code', $cart->coupon_code)->first();
                if ($coupon) {
                    $this->couponService->recordUsage($coupon, $user, $order, $discountAmount);
                }
            }

            // 10. Initial Order History
            $order->addHistory('pending', 'Order placed successfully by customer.', $user->id, true);

            // 11. Clear Cart
            $this->cartService->clearCart($cart);

            return $order->load(['items.product.media', 'items.variant', 'histories']);
        });
    }

    /**
     * Update order status with auto stock restoration if cancelled
     */
    public function updateOrderStatus(Order $order, string $newStatus, ?string $comment = null, ?int $userId = null): Order
    {
        $oldStatus = $order->status;

        if ($oldStatus === $newStatus) {
            return $order;
        }

        $order->update(['status' => $newStatus]);

        if ($newStatus === 'shipped') {
            $order->update(['shipped_at' => now()]);
        } elseif ($newStatus === 'delivered') {
            $order->update(['delivered_at' => now(), 'payment_status' => 'paid', 'paid_at' => $order->paid_at ?: now()]);
        } elseif (in_array($newStatus, ['cancelled', 'refunded']) && ! in_array($oldStatus, ['cancelled', 'refunded'])) {
            $order->update(['cancelled_at' => now()]);

            // Auto-restore inventory stock
            foreach ($order->items as $item) {
                $this->inventoryService->adjustStock(
                    $item->product_id,
                    $item->variant_id,
                    $item->quantity,
                    'increment'
                );
            }
        }

        $order->addHistory($newStatus, $comment ?: "Order status changed from {$oldStatus} to {$newStatus}.", $userId, true);

        return $order->fresh(['items', 'histories']);
    }

    /**
     * Customer order cancellation
     */
    public function cancelOrder(Order $order, User $user, string $reason): Order
    {
        if (! $order->canBeCancelled()) {
            throw new Exception("Order cannot be cancelled because it is already {$order->status}.");
        }

        if ($order->user_id !== $user->id) {
            throw new Exception('Unauthorized to cancel this order.');
        }

        return $this->updateOrderStatus($order, 'cancelled', "Cancelled by customer: {$reason}", $user->id);
    }

    /**
     * Admin Order List with Multi-filters
     */
    public function getAdminOrders(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = Order::with(['user:id,name,email,avatar', 'items', 'shippingMethod']);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        if (! empty($filters['search'])) {
            $s = '%' . $filters['search'] . '%';
            $query->where(function ($q) use ($s) {
                $q->where('order_number', 'like', $s)
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', $s)->orWhere('email', 'like', $s));
            });
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Executive E-Commerce Sales Analytics
     */
    public function getEcommerceAnalytics(): array
    {
        $totalRevenue = (float) Order::where('payment_status', 'paid')->sum('total_amount');
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $completedOrders = Order::where('status', 'delivered')->count();
        $averageOrderValue = $totalOrders > 0 ? round($totalRevenue / max(1, $completedOrders), 2) : 0.00;

        // Today's Sales
        $todayRevenue = (float) Order::where('payment_status', 'paid')->whereDate('created_at', today())->sum('total_amount');
        $todayOrders = Order::whereDate('created_at', today())->count();

        // Monthly Sales Trend (Last 6 months)
        $monthlyTrend = Order::where('payment_status', 'paid')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(total_amount) as revenue, COUNT(*) as orders_count')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->take(6)
            ->get();

        // Top 5 Selling Products
        $topProducts = OrderItem::select('product_id', 'product_name', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(subtotal) as total_revenue'))
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        return [
            'overview' => [
                'total_revenue' => $totalRevenue,
                'total_orders' => $totalOrders,
                'pending_orders' => $pendingOrders,
                'completed_orders' => $completedOrders,
                'average_order_value' => $averageOrderValue,
                'today_revenue' => $todayRevenue,
                'today_orders' => $todayOrders,
            ],
            'monthly_trend' => $monthlyTrend,
            'top_selling_products' => $topProducts,
        ];
    }
}
