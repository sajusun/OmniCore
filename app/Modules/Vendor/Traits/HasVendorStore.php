<?php

declare(strict_types=1);

namespace App\Modules\Vendor\Traits;

use App\Modules\Vendor\Models\VendorMember;
use App\Modules\Vendor\Models\VendorStore;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasVendorStore
{
    public function vendorStore(): HasOne
    {
        return $this->hasOne(VendorStore::class, 'user_id');
    }

    public function vendorMemberships(): HasMany
    {
        return $this->hasMany(VendorMember::class, 'user_id');
    }

    public function isVendor(): bool
    {
        return $this->vendorStore()->where('status', 'active')->exists();
    }
}
