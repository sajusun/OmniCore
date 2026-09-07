<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Cart\Models\Cart;
use App\Modules\Cart\Services\CartService;
use App\Modules\Order\Enums\OrderStatus;
use App\Modules\Order\Models\Order;
use App\Modules\Order\Services\OrderService;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\ProductVariant;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderCheckoutConcurrencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_atomic_checkout_decrements_stock_and_clears_cart(): void
    {
        $user = User::factory()->create();

        $product = Product::create([
            'name' => 'Flagship Mechanical Keyboard',
            'slug' => 'flagship-mechanical-keyboard',
            'sku' => 'KB-MECH-001',
            'price' => 150.00,
            'stock_quantity' => 10,
            'manage_stock' => true,
            'is_in_stock' => true,
            'status' => 'active',
        ]);

        $cartService = app(CartService::class);
        $orderService = app(OrderService::class);

        $cart = $cartService->getOrCreateCart($user);
        $cartService->addItem($cart, $product->id, null, 2);

        $this->assertEquals(1, $cart->items()->count());

        $order = $orderService->checkout($user, [
            'payment_method' => 'cod',
            'shipping_address' => [
                'recipient_name' => 'John Doe',
                'phone' => '+123456789',
                'street_address' => '123 Tech Avenue',
                'city' => 'Silicon Valley',
                'state' => 'CA',
                'postal_code' => '94025',
                'country' => 'USA',
            ],
        ]);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals('pending', $order->status);
        $this->assertEquals(300.00, (float) $order->subtotal);

        // Verify product stock decremented atomically
        $product->refresh();
        $this->assertEquals(8, $product->stock_quantity);

        // Verify cart cleared
        $cart->refresh();
        $this->assertTrue($cart->items()->count() === 0);
    }

    public function test_order_state_machine_validates_transitions(): void
    {
        $user = User::factory()->create();

        $order = Order::create([
            'order_number' => 'ORD-TEST-001',
            'user_id' => $user->id,
            'status' => OrderStatus::PENDING->value,
            'payment_status' => 'unpaid',
            'payment_method' => 'cod',
            'subtotal' => 100.00,
            'discount_amount' => 0.00,
            'shipping_fee' => 0.00,
            'tax_amount' => 0.00,
            'total_amount' => 100.00,
            'shipping_address' => ['recipient_name' => 'Jane'],
        ]);

        $orderService = app(OrderService::class);

        // Valid transition: Pending -> Confirmed
        $orderService->updateOrderStatus($order, OrderStatus::CONFIRMED);
        $this->assertEquals(OrderStatus::CONFIRMED->value, $order->refresh()->status);

        // Valid transition: Confirmed -> Processing -> Shipped -> Delivered
        $orderService->updateOrderStatus($order, OrderStatus::PROCESSING);
        $orderService->updateOrderStatus($order, OrderStatus::SHIPPED);
        $orderService->updateOrderStatus($order, OrderStatus::DELIVERED);
        $this->assertEquals(OrderStatus::DELIVERED->value, $order->refresh()->status);

        // Invalid transition: Delivered -> Pending should throw Exception
        $this->expectException(Exception::class);
        $orderService->updateOrderStatus($order, OrderStatus::PENDING);
    }

    public function test_order_cancellation_restores_inventory_stock(): void
    {
        $user = User::factory()->create();

        $product = Product::create([
            'name' => 'Studio Monitor Headphones',
            'slug' => 'studio-monitor-headphones',
            'sku' => 'HP-STUDIO-002',
            'price' => 200.00,
            'stock_quantity' => 5,
            'manage_stock' => true,
            'is_in_stock' => true,
            'status' => 'active',
        ]);

        $cartService = app(CartService::class);
        $orderService = app(OrderService::class);

        $cart = $cartService->getOrCreateCart($user);
        $cartService->addItem($cart, $product->id, null, 3);

        $order = $orderService->checkout($user, [
            'payment_method' => 'cod',
            'shipping_address' => [
                'recipient_name' => 'Bob Smith',
                'phone' => '+198765432',
                'street_address' => '456 Audio Lane',
                'city' => 'Austin',
                'state' => 'TX',
                'postal_code' => '73301',
                'country' => 'USA',
            ],
        ]);

        $product->refresh();
        $this->assertEquals(2, $product->stock_quantity);

        // Cancel order -> Stock should be restored
        $orderService->cancelOrder($order, $user, 'Customer changed mind');

        $this->assertEquals('cancelled', $order->refresh()->status);
        $product->refresh();
        $this->assertEquals(5, $product->stock_quantity);
    }
}
