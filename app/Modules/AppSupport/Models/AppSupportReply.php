<?php

namespace App\Modules\AppSupport\Models;

use App\Models\User;
use App\Modules\Media\Traits\HasMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppSupportReply extends Model
{
    use HasFactory, HasMedia;

    protected $table = 'app_support_replies';

    protected $fillable = [
        'app_support_id',
        'sender_type',
        'user_id',
        'message',
    ];

    /**
     * Parent support report.
     */
    public function support(): BelongsTo
    {
        return $this->belongsTo(AppSupport::class, 'app_support_id');
    }

    /**
     * User/Admin who authored the reply (null for system).
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
