<?php

declare(strict_types=1);

namespace App\Modules\Vendor\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorMember extends Model
{
    use HasFactory;

    protected $table = 'vendor_members';

    protected $fillable = [
        'vendor_id',
        'user_id',
        'role', // owner, manager, staff
        'permissions',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(VendorStore::class, 'vendor_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
