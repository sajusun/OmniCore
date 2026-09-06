<?php

declare(strict_types=1);

namespace App\Modules\Affiliate\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AffiliateCommission extends Model
{
    use HasFactory;

    protected $table = 'affiliate_commissions';

    protected $fillable = [
        'affiliate_id',
        'referral_id',
        'reference_type',
        'reference_id',
        'order_amount',
        'commission_rate',
        'commission_amount',
        'status', // pending, approved, paid, rejected
        'approved_at',
        'paid_at',
    ];

    protected $casts = [
        'order_amount'      => 'decimal:2',
        'commission_rate'   => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'approved_at'       => 'datetime',
        'paid_at'           => 'datetime',
    ];

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(AffiliateAccount::class, 'affiliate_id');
    }

    public function referral(): BelongsTo
    {
        return $this->belongsTo(AffiliateReferral::class, 'referral_id');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}
