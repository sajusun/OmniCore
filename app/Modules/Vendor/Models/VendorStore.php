<?php

declare(strict_types=1);

namespace App\Modules\Vendor\Models;

use App\Models\User;
use App\Modules\Product\Models\Product;
use App\Modules\Review\Traits\HasReviews;
use App\Modules\Vendor\Enums\VendorStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VendorStore extends Model
{
    use HasFactory, HasReviews;

    protected $table = 'vendor_stores';

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'logo_path',
        'banner_path',
        'phone',
        'email',
        'address',
        'commission_rate',
        'status',
        'total_sales',
        'total_earnings',
        'balance',
        'is_featured',
        'payout_settings',
        'business_documents',
    ];

    protected $casts = [
        'commission_rate'    => 'decimal:2',
        'status'             => VendorStatus::class,
        'total_sales'        => 'decimal:2',
        'total_earnings'     => 'decimal:2',
        'balance'            => 'decimal:2',
        'is_featured'        => 'boolean',
        'payout_settings'    => 'array',
        'business_documents' => 'array',
    ];

    protected $appends = ['logo_url', 'banner_url'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($store) {
            if (empty($store->slug)) {
                $store->slug = Str::slug($store->name);
            }
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(VendorMember::class, 'vendor_id');
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(VendorPayout::class, 'vendor_id');
    }

    public function getLogoUrlAttribute(): ?string
    {
        if (empty($this->logo_path)) {
            return null;
        }
        if (filter_var($this->logo_path, FILTER_VALIDATE_URL)) {
            return $this->logo_path;
        }
        return Storage::url($this->logo_path);
    }

    public function getBannerUrlAttribute(): ?string
    {
        if (empty($this->banner_path)) {
            return null;
        }
        if (filter_var($this->banner_path, FILTER_VALIDATE_URL)) {
            return $this->banner_path;
        }
        return Storage::url($this->banner_path);
    }

    public function scopeActive($query)
    {
        return $query->where('status', VendorStatus::ACTIVE->value);
    }
}
