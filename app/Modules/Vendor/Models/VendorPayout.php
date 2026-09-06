<?php

declare(strict_types=1);

namespace App\Modules\Vendor\Models;

use App\Models\User;
use App\Modules\Vendor\Enums\PayoutStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorPayout extends Model
{
    use HasFactory;

    protected $table = 'vendor_payouts';

    protected $fillable = [
        'vendor_id',
        'requested_by',
        'amount',
        'status',
        'payout_method',
        'transaction_id',
        'admin_note',
        'processed_at',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'status'       => PayoutStatus::class,
        'processed_at' => 'datetime',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(VendorStore::class, 'vendor_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
}
