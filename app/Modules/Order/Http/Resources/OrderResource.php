<?php

namespace App\Modules\Order\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'payment_method' => $this->payment_method,
            'transaction_id' => $this->transaction_id,
            'subtotal' => (float) $this->subtotal,
            'discount_amount' => (float) $this->discount_amount,
            'coupon_code' => $this->coupon_code,
            'shipping_fee' => (float) $this->shipping_fee,
            'tax_amount' => (float) $this->tax_amount,
            'total_amount' => (float) $this->total_amount,
            'shipping_address' => $this->shipping_address,
            'billing_address' => $this->billing_address,
            'customer_notes' => $this->customer_notes,
            'can_be_cancelled' => $this->canBeCancelled(),
            'paid_at' => $this->paid_at?->toIso8601String(),
            'shipped_at' => $this->shipped_at?->toIso8601String(),
            'delivered_at' => $this->delivered_at?->toIso8601String(),
            'cancelled_at' => $this->cancelled_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'customer' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'avatar' => $this->user->avatar,
            ]),
            'shipping_method' => $this->whenLoaded('shippingMethod', fn () => [
                'id' => $this->shippingMethod->id,
                'name' => $this->shippingMethod->name,
                'estimated_delivery_days' => $this->shippingMethod->estimated_delivery_days,
            ]),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'timeline' => $this->whenLoaded('histories', function () {
                return $this->histories->map(fn ($h) => [
                    'status' => $h->status,
                    'comment' => $h->comment,
                    'created_at' => $h->created_at?->toIso8601String(),
                ]);
            }),
        ];
    }
}
