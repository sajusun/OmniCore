<?php

namespace App\Modules\Product\Models;

use App\Models\User;
use App\Modules\Media\Traits\HasMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductReview extends Model
{
    use HasFactory, HasMedia, SoftDeletes;

    protected $table = 'product_reviews';

    protected $fillable = [
        'product_id',
        'user_id',
        'rating',
        'title',
        'comment',
        'is_verified_purchase',
        'status', // pending, approved, rejected
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_verified_purchase' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
