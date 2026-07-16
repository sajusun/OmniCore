<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Verification extends Model
{


    public const string STATUS_PENDING  = 'pending';
    public const string STATUS_VERIFIED = 'verified';
    public const string STATUS_EXPIRED  = 'expired';



    public const string TYPE_OTP   = 'otp';
    public const string TYPE_TOKEN = 'token';


    public const string PURPOSE_EMAIL_VERIFICATION = 'email_verification';
    public const string PURPOSE_PASSWORD_RESET     = 'password_reset';

    // -------------------------------------------------------------------------
    // Eloquent configuration
    // -------------------------------------------------------------------------

    protected $table = 'verifications';

    protected $fillable = [
        'user_id',
        'verification_type',
        'purpose',
        'code',
        'attempts',
        'request_count',
        'last_requested_at',
        'blocked_until',
        'expires_at',
        'verified_at',
        'status',
    ];

    protected $casts = [
        'attempts'          => 'integer',
        'request_count'     => 'integer',
        'last_requested_at' => 'datetime',
        'blocked_until'     => 'datetime',
        'expires_at'        => 'datetime',
        'verified_at'       => 'datetime',
    ];

    /**
     * The attributes that should be hidden in serialization
     * (never leak the code hash to API consumers).
     *
     * @var list<string>
     */
    protected $hidden = [
        'code',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * The user this verification belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // -------------------------------------------------------------------------
    // Domain helpers
    // -------------------------------------------------------------------------

    /**
     * Determine whether this verification record has passed its expiry time.
     */
    public function isExpired(): bool
    {
        return $this->expires_at !== null
            && Carbon::now()->greaterThan($this->expires_at);
    }

    /**
     * Determine whether the user is currently in a block window.
     */
    public function isBlocked(): bool
    {
        return $this->blocked_until !== null
            && Carbon::now()->lessThan($this->blocked_until);
    }

    /**
     * Determine whether this record has already been successfully verified.
     */
    public function isVerified(): bool
    {
        return $this->status === self::STATUS_VERIFIED
            && $this->verified_at !== null;
    }

    /**
     * Determine whether this record is still in a pending state.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
}
