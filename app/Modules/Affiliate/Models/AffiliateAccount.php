<?php

declare(strict_types=1);

namespace App\Modules\Affiliate\Models;

use App\Models\User;
use App\Modules\Affiliate\Enums\AffiliateStatus;
use App\Modules\Affiliate\Enums\CommissionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class AffiliateAccount extends Model
{
    use HasFactory;

    protected $table = 'affiliate_accounts';

    protected $fillable = [
        'user_id',
        'referral_code',
        'custom_slug',
        'commission_rate',
        'commission_type',
        'status',
        'total_earnings',
        'paid_earnings',
        'current_balance',
        'lifetime_referrals',
        'lifetime_conversions',
        'payout_settings',
    ];

    protected $casts = [
        'commission_rate'      => 'decimal:2',
        'commission_type'      => CommissionType::class,
        'status'               => AffiliateStatus::class,
        'total_earnings'       => 'decimal:2',
        'paid_earnings'        => 'decimal:2',
        'current_balance'      => 'decimal:2',
        'lifetime_referrals'   => 'integer',
        'lifetime_conversions' => 'integer',
        'payout_settings'      => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($account) {
            if (empty($account->referral_code)) {
                $account->referral_code = 'REF-' . strtoupper(Str::random(6));
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(AffiliateReferral::class, 'affiliate_id');
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(AffiliateCommission::class, 'affiliate_id');
    }

    public function getReferralUrlAttribute(): string
    {
        return url('/?ref=' . ($this->custom_slug ?: $this->referral_code));
    }
}
