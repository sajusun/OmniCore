<?php

declare(strict_types=1);

namespace App\Modules\Cart\Services;

use App\Models\User;
use App\Modules\Cart\Models\Cart;
use App\Modules\Cart\Models\CartItem;
use App\Modules\Coupon\Models\Coupon;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\ProductVariant;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CartService
{
    /**
     * Get or initialize Cart for user or guest
     */
    public function getOrCreateCart(?User $user, ?string $guestToken = null): Cart
    {
        if ($user) {
            $cart = Cart::with(['items.product.media', 'items.variant'])->firstOrCreate(
                ['user_id' => $user->id]
            );

            // If a guest token was passed alongside logged-in user, sync it
            if ($guestToken) {
                $this->syncGuestCart($user, $guestToken);

                return $cart->fresh(['items.product.media', 'items.variant']);
            }

            return $cart;
        }

        if (empty($guestToken)) {
            $guestToken = (string) Str::uuid();
        }

        return Cart::with(['items.product.media', 'items.variant'])->firstOrCreate(
            ['guest_token' => $guestToken]
        );
    }

    /**
     * Add item to cart with atomic stock validation
     */
    public function addItem(Cart $cart, int $productId, ?int $variantId = null, int $quantity = 1): CartItem
    {
        return DB::transaction(function () use ($cart, $productId, $variantId, $quantity) {
            $product = Product::where('id', $productId)->lockForUpdate()->firstOrFail();
            $variant = $variantId ? ProductVariant::where('product_id', $productId)->where('id', $variantId)->lockForUpdate()->firstOrFail() : null;

            // 1. Stock Validation
            $availableStock = $variant ? $variant->stock_quantity : $product->stock_quantity;
            $manageStock = $variant ? $variant->manage_stock : $product->manage_stock;

            if ($manageStock && $availableStock < $quantity) {
                throw new Exception("Only {$availableStock} units available in stock.");
            }

            // 2. Determine Unit Price
            $unitPrice = $variant ? (float) $variant->price : (float) $product->price;

            // 3. Find existing item or create new
            $item = $cart->items()
                ->where('product_id', $productId)
                ->where('variant_id', $variantId)
                ->first();

            if ($item) {
                $newQuantity = $item->quantity + $quantity;
                if ($manageStock && $availableStock < $newQuantity) {
                    throw new Exception("Cannot add more. You already have {$item->quantity} in your cart, and only {$availableStock} are available.");
                }
                $item->update([
                    'quantity' => $newQuantity,
                    'unit_price' => $unitPrice,
                ]);
            } else {
                $item = $cart->items()->create([
                    'product_id' => $productId,
                    'variant_id' => $variantId,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                ]);
            }

            // Re-evaluate coupon if applied
            $this->recalculateCouponDiscount($cart);

            return $item->load(['product.media', 'variant.attributeValues.attribute']);
        });
    }

    /**
     * Update quantity of item in cart with atomic stock check
     */
    public function updateItemQuantity(CartItem $item, int $quantity): CartItem
    {
        return DB::transaction(function () use ($item, $quantity) {
            if ($quantity <= 0) {
                $cart = $item->cart;
                $item->delete();
                $this->recalculateCouponDiscount($cart);

                return $item;
            }

            $product = Product::where('id', $item->product_id)->lockForUpdate()->firstOrFail();
            $variant = $item->variant_id ? ProductVariant::where('product_id', $item->product_id)->where('id', $item->variant_id)->lockForUpdate()->firstOrFail() : null;

            $availableStock = $variant ? $variant->stock_quantity : $product->stock_quantity;
            $manageStock = $variant ? $variant->manage_stock : $product->manage_stock;

            if ($manageStock && $availableStock < $quantity) {
                throw new Exception("Only {$availableStock} units available in stock.");
            }

            $item->update(['quantity' => $quantity]);
            $this->recalculateCouponDiscount($item->cart);

            return $item;
        });
    }

    /**
     * Remove item
     */
    public function removeItem(CartItem $item): bool
    {
        $cart = $item->cart;
        $deleted = $item->delete();
        $this->recalculateCouponDiscount($cart);

        return $deleted;
    }

    /**
     * Clear all items in cart
     */
    public function clearCart(Cart $cart): bool
    {
        $cart->items()->delete();
        $cart->update([
            'coupon_code' => null,
            'discount_amount' => 0.00,
        ]);

        return true;
    }

    /**
     * Merge guest cart into user cart upon login
     */
    public function syncGuestCart(User $user, string $guestToken): Cart
    {
        $guestCart = Cart::with('items')->where('guest_token', $guestToken)->first();
        $userCart = Cart::with('items')->firstOrCreate(['user_id' => $user->id]);

        if (! $guestCart || $guestCart->items->isEmpty()) {
            return $userCart;
        }

        foreach ($guestCart->items as $guestItem) {
            $existingUserItem = $userCart->items()
                ->where('product_id', $guestItem->product_id)
                ->where('variant_id', $guestItem->variant_id)
                ->first();

            if ($existingUserItem) {
                $existingUserItem->update([
                    'quantity' => $existingUserItem->quantity + $guestItem->quantity,
                ]);
            } else {
                $guestItem->update(['cart_id' => $userCart->id]);
            }
        }

        // Delete empty guest cart
        $guestCart->delete();
        $this->recalculateCouponDiscount($userCart);

        return $userCart->fresh(['items.product.media', 'items.variant']);
    }

    /**
     * Apply coupon to cart
     */
    public function applyCoupon(Cart $cart, string $code, ?User $user = null): array
    {
        $coupon = Coupon::where('code', Str::upper($code))->first();

        if (! $coupon) {
            return ['success' => false, 'message' => 'Invalid coupon code.'];
        }

        $subtotal = $cart->subtotal;
        $validation = $coupon->isValid($user, $subtotal);

        if (! $validation['valid']) {
            return ['success' => false, 'message' => $validation['message']];
        }

        $discount = $coupon->calculateDiscount($subtotal);

        $cart->update([
            'coupon_code' => $coupon->code,
            'discount_amount' => $discount,
        ]);

        return [
            'success' => true,
            'message' => 'Coupon applied successfully!',
            'coupon_code' => $coupon->code,
            'discount_amount' => $discount,
            'grand_total' => $cart->grand_total,
        ];
    }

    /**
     * Remove coupon
     */
    public function removeCoupon(Cart $cart): Cart
    {
        $cart->update([
            'coupon_code' => null,
            'discount_amount' => 0.00,
        ]);

        return $cart;
    }

    /**
     * Recalculate coupon discount on cart change
     */
    protected function recalculateCouponDiscount(Cart $cart): void
    {
        if (empty($cart->coupon_code)) {
            return;
        }

        $coupon = Coupon::where('code', $cart->coupon_code)->first();
        if (! $coupon || ! $coupon->is_active) {
            $this->removeCoupon($cart);

            return;
        }

        $subtotal = $cart->subtotal;
        if ($coupon->min_order_amount && $subtotal < $coupon->min_order_amount) {
            $this->removeCoupon($cart);

            return;
        }

        $discount = $coupon->calculateDiscount($subtotal);
        $cart->update(['discount_amount' => $discount]);
    }
}
