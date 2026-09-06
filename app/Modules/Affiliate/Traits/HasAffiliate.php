<?php

declare(strict_types=1);

namespace App\Modules\Affiliate\Traits;

use App\Modules\Affiliate\Models\AffiliateAccount;
use App\Modules\Affiliate\Models\AffiliateReferral;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasAffiliate
{
    public function affiliateAccount(): HasOne
    {
        return $this->hasOne(AffiliateAccount::class, 'user_id');
    }

    public function referredBy(): HasMany
    {
        return $this->hasMany(AffiliateReferral::class, 'referred_user_id');
    }
}
