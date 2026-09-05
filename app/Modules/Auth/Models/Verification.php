<?php

declare(strict_types=1);

namespace App\Modules\Auth\Models;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Verification extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_VERIFIED = 'verified';

    public const STATUS_EXPIRED = 'expired';

    public const TYPE_OTP = 'otp';

    public const TYPE_TOKEN = 'token';

    public const PURPOSE_EMAIL_VERIFICATION = 'email_verification';

    public const PURPOSE_PASSWORD_RESET = 'password_reset';

    protected $table = 'verifications';

    /**
     * Temporary holder for unhashed token during mailing.
     */
    public ?string $plain_token = null;

    protected $fillable = [
        'verifiable_type',
        'verifiable_id',
        'user_id',
        'verification_type',
        'purpose',
        'code',
        'channel',
        'attempts',
        'request_count',
        'last_requested_at',
        'blocked_until',
        'expires_at',
        'verified_at',
        'status',
    ];

    protected $casts = [
        'attempts' => 'integer',
        'request_count' => 'integer',
        'last_requested_at' => 'datetime',
        'blocked_until' => 'datetime',
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    protected $hidden = [
        'code',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * Polymorphic parent model (User, Admin, Vendor, etc.)
     */
    public function verifiable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Backward-compatible User relationship.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // -------------------------------------------------------------------------
    // Domain Helpers
    // -------------------------------------------------------------------------

    public function isExpired(): bool
    {
        return $this->expires_at !== null
            && Carbon::now()->greaterThan($this->expires_at);
    }

    public function isBlocked(): bool
    {
        return $this->blocked_until !== null
            && Carbon::now()->lessThan($this->blocked_until);
    }

    public function isVerified(): bool
    {
        return $this->status === self::STATUS_VERIFIED
            && $this->verified_at !== null;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
}
