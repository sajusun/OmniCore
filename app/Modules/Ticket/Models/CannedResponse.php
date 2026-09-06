<?php

declare(strict_types=1);

namespace App\Modules\Ticket\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CannedResponse extends Model
{
    use HasFactory;

    protected $table = 'canned_responses';

    protected $fillable = [
        'title',
        'shortcut',
        'body',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
