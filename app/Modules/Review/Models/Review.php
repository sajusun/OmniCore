<?php

declare(strict_types=1);

namespace App\Modules\Review\Models;

use App\Models\User;
use App\Modules\Review\Enums\ReviewStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Review extends Model
{
    use HasFactory;

    protected $table = 'reviews';

    protected $fillable = [
        'user_id',
        'reviewable_type',
        'reviewable_id',
        'rating',
        'title',
        'comment',
        'criteria_ratings',
        'is_verified_buyer',
        'status',
        'helpful_count',
        'unhelpful_count',
        'vendor_reply',
        'vendor_replied_at',
    ];

    protected $casts = [
        'rating'            => 'integer',
        'criteria_ratings'  => 'array',
        'is_verified_buyer' => 'boolean',
        'status'            => ReviewStatus::class,
        'helpful_count'     => 'integer',
        'unhelpful_count'   => 'integer',
        'vendor_replied_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }

    public function media(): HasMany
    {
        return $this->hasMany(ReviewMedia::class, 'review_id')->orderBy('sort_order', 'asc');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(ReviewVote::class, 'review_id');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', ReviewStatus::APPROVED->value);
    }
}
