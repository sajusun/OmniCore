<?php

namespace App\Modules\Reward\Models;

use App\Models\User;
use App\Modules\Reward\Enums\PointTransactionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class PointTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'amount',
        'type',
        'before_balance',
        'after_balance',
        'description',
        'reference_type',
        'reference_id',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'integer',
        'before_balance' => 'integer',
        'after_balance' => 'integer',
        'metadata' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function ($trx) {
            if (empty($trx->uuid)) {
                $trx->uuid = (string) Str::uuid();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}
