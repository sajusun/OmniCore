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
    }

    /**
     * Get current cart (User or Guest)
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $guestToken = $request->header('X-Guest-Token', $request->query('guest_token'));

        $cart = $this->cartService->getOrCreateCart($user, $guestToken);

        return response()->json([
            'success' => true,
            'data' => new CartResource($cart),
        ]);
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

            return response()->json([
                'success' => true,
                'message' => 'Item added to cart.',
                'guest_token' => $cart->guest_token,
                'data' => new CartItemResource($item),
                'cart' => new CartResource($cart->fresh('items.product.media')),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
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

            return response()->json([
                'success' => true,
                'message' => 'Cart updated.',
                'cart' => new CartResource($item->cart->fresh('items.product.media')),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
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

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart.',
            'cart' => new CartResource($cart->fresh('items.product.media')),
        ]);
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

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared.',
            'cart' => new CartResource($cart->fresh('items')),
        ]);
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

        return response()->json([
            'success' => true,
            'message' => 'Guest cart synced successfully.',
            'data' => new CartResource($cart),
        ]);
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
            return response()->json($result, 422);
        }

        return response()->json(array_merge($result, [
            'cart' => new CartResource($cart->fresh('items.product')),
        ]));
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

        return response()->json([
            'success' => true,
            'message' => 'Coupon removed.',
            'cart' => new CartResource($cart->fresh('items.product')),
        ]);
    }
}
