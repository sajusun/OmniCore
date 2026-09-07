<?php

namespace App\Modules\Cart\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Cart\Http\Resources\CartItemResource;
use App\Modules\Cart\Http\Resources\CartResource;
use App\Modules\Cart\Models\CartItem;
use App\Modules\Cart\Services\CartService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(protected CartService $cartService)
    {
        parent::__construct();
    }

    /**
     * Get current cart (User or Guest)
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $guestToken = $request->header('X-Guest-Token', $request->query('guest_token'));

        $cart = $this->cartService->getOrCreateCart($user, $guestToken);

        return $this->success(
            new CartResource($cart),
            'Cart retrieved successfully.'
        );
    }

    /**
     * Add item to cart
     */
    public function addItem(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'variant_id' => 'nullable|integer|exists:product_variants,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $user = $request->user();
        $guestToken = $request->header('X-Guest-Token', $request->input('guest_token'));
        $cart = $this->cartService->getOrCreateCart($user, $guestToken);

        try {
            $item = $this->cartService->addItem(
                $cart,
                (int) $request->product_id,
                $request->variant_id ? (int) $request->variant_id : null,
                (int) $request->get('quantity', 1)
            );

            return $this->success([
                'guest_token' => $cart->guest_token,
                'item' => new CartItemResource($item),
                'cart' => new CartResource($cart->fresh('items.product.media')),
            ], 'Item added to cart.', 201);
        } catch (Exception $e) {
            return $this->error($e->getMessage(), null, 422);
        }
    }

    /**
     * Update item quantity
     */
    public function updateItem(int $itemId, Request $request): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $item = CartItem::with('cart')->findOrFail($itemId);

        try {
            $this->cartService->updateItemQuantity($item, (int) $request->quantity);

            return $this->success(
                new CartResource($item->cart->fresh('items.product.media')),
                'Cart updated.'
            );
        } catch (Exception $e) {
            return $this->error($e->getMessage(), null, 422);
        }
    }

    /**
     * Remove item from cart
     */
    public function removeItem(int $itemId): JsonResponse
    {
        $item = CartItem::with('cart')->findOrFail($itemId);
        $cart = $item->cart;
        $this->cartService->removeItem($item);

        return $this->success(
            new CartResource($cart->fresh('items.product.media')),
            'Item removed from cart.'
        );
    }

    /**
     * Clear all items in cart
     */
    public function clear(Request $request): JsonResponse
    {
        $user = $request->user();
        $guestToken = $request->header('X-Guest-Token', $request->input('guest_token'));
        $cart = $this->cartService->getOrCreateCart($user, $guestToken);

        $this->cartService->clearCart($cart);

        return $this->success(
            new CartResource($cart->fresh('items')),
            'Cart cleared.'
        );
    }

    /**
     * Merge guest cart to authenticated user
     */
    public function sync(Request $request): JsonResponse
    {
        $request->validate([
            'guest_token' => 'required|string',
        ]);

        $user = $request->user();
        $cart = $this->cartService->syncGuestCart($user, $request->guest_token);

        return $this->success(
            new CartResource($cart),
            'Guest cart synced successfully.'
        );
    }

    /**
     * Apply coupon
     */
    public function applyCoupon(Request $request): JsonResponse
    {
        $request->validate([
            'coupon_code' => 'required|string',
        ]);

        $user = $request->user();
        $guestToken = $request->header('X-Guest-Token', $request->input('guest_token'));
        $cart = $this->cartService->getOrCreateCart($user, $guestToken);

        $result = $this->cartService->applyCoupon($cart, $request->coupon_code, $user);

        if (! $result['success']) {
            return $this->error($result['message'] ?? 'Coupon could not be applied.', null, 422);
        }

        return $this->success(
            array_merge($result, [
                'cart' => new CartResource($cart->fresh('items.product')),
            ]),
            $result['message'] ?? 'Coupon applied successfully.'
        );
    }

    /**
     * Remove coupon
     */
    public function removeCoupon(Request $request): JsonResponse
    {
        $user = $request->user();
        $guestToken = $request->header('X-Guest-Token', $request->input('guest_token'));
        $cart = $this->cartService->getOrCreateCart($user, $guestToken);

        $this->cartService->removeCoupon($cart);

        return $this->success(
            new CartResource($cart->fresh('items.product')),
            'Coupon removed.'
        );
    }
}
