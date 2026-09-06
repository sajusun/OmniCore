<?php

declare(strict_types=1);

namespace App\Modules\Affiliate\Models;

use App\Models\User;
use App\Modules\Affiliate\Enums\ReferralStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AffiliateReferral extends Model
{
    use HasFactory;

    protected $table = 'affiliate_referrals';

    protected $fillable = [
        'affiliate_id',
        'referred_user_id',
        'visitor_ip',
        'user_agent',
        'landing_page',
        'campaign',
        'status',
        'converted_at',
    ];

    protected $casts = [
        'status'       => ReferralStatus::class,
        'converted_at' => 'datetime',
    ];

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(AffiliateAccount::class, 'affiliate_id');
    }

    public function referredUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(AffiliateCommission::class, 'referral_id');
    }
}
