<?php

namespace App\Modules\AppSupport\Models;

use App\Models\User;
use App\Modules\AppSupport\Enums\SupportCategory;
use App\Modules\AppSupport\Enums\SupportStatus;
use App\Modules\Media\Traits\HasMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AppSupport extends Model
{
    use HasFactory, HasMedia;

    protected $table = 'app_supports';

    protected $fillable = [
        'ticket_no',
        'user_id',
        'subject',
        'category',
        'message',
        'device_os',
        'device_model',
        'app_version',
        'status',
    ];

    protected $casts = [
        'category' => SupportCategory::class,
        'status' => SupportStatus::class,
    ];

    /**
     * The user who submitted the support request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * All replies / conversation history.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(AppSupportReply::class, 'app_support_id')->orderBy('created_at', 'asc');
    }

    /**
     * Latest reply.
     */
    public function latestReply()
    {
        return $this->hasOne(AppSupportReply::class, 'app_support_id')->latestOfMany();
    }

    /**
     * Query scope: filter by status.
     */
    public function scopeStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    /**
     * Query scope: filter by category.
     */
    public function scopeCategory($query, $category)
    {
        if ($category) {
            return $query->where('category', $category);
        }
        return $query;
    }

    /**
     * Query scope: search by keyword.
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('ticket_no', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        return $query;
    }
}
