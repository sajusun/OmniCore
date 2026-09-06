<?php

namespace App\Modules\Subscription\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanFeature extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_id',
        'name',
        'code',
        'value',
        'is_limited',
        'sort_order',
    ];

    protected $casts = [
        'is_limited' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function isUnlimited(): bool
    {
        return strtolower((string) $this->value) === 'unlimited' || (strtolower((string) $this->value) === 'true' && !$this->is_limited);
    }

    public function getQuota(): int
    {
        return is_numeric($this->value) ? (int) $this->value : ($this->isUnlimited() ? PHP_INT_MAX : 0);
    }
}
