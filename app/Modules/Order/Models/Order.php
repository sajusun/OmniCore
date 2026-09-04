<?php

namespace App\Modules\Order\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'orders';

    protected $fillable = [
        'order_number',
        'user_id',
        'status', // pending, confirmed, processing, shipped, out_for_delivery, delivered, cancelled, refunded
        'payment_status', // unpaid, paid, refunded, failed
        'payment_method', // cod, stripe, sslcommerz, bkash, bank_transfer
        'transaction_id',
        'subtotal',
        'discount_amount',
        'coupon_code',
        'shipping_fee',
        'tax_amount',
        'total_amount',
        'shipping_method_id',
        'shipping_address',
        'billing_address',
        'customer_notes',
        'admin_notes',
        'paid_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'shipping_address' => 'array',
        'billing_address' => 'array',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(OrderHistory::class, 'order_id')->latest();
    }

    public function shippingMethod(): BelongsTo
    {
        return $this->belongsTo(ShippingMethod::class, 'shipping_method_id');
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function addHistory(string $status, ?string $comment = null, ?int $userId = null, bool $notified = false): OrderHistory
    {
        return $this->histories()->create([
            'status' => $status,
            'comment' => $comment,
            'user_id' => $userId,
            'customer_notified' => $notified,
        ]);
    }
}
