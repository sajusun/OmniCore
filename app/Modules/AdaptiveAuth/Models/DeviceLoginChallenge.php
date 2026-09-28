<?php

declare(strict_types=1);

namespace App\Modules\AdaptiveAuth\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Hash;

class DeviceLoginChallenge extends Model
{
    use HasFactory;

    protected $table = 'device_login_challenges';

    protected $fillable = [
        'challenge_token',
        'authenticatable_type',
        'authenticatable_id',
        'device_uuid',
        'device_metadata',
        'otp_code_hash',
        'attempts',
        'max_attempts',
        'resend_available_at',
        'expires_at',
        'verified_at',
        'remember_device',
    ];

    protected $casts = [
        'device_metadata'     => 'array',
        'attempts'            => 'integer',
        'max_attempts'        => 'integer',
        'resend_available_at' => 'datetime',
        'expires_at'          => 'datetime',
        'verified_at'         => 'datetime',
        'remember_device'     => 'boolean',
    ];

    public function authenticatable(): MorphTo
    {
        return $this->morphTo();
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }

    public function hasExceededAttempts(): bool
    {
        return $this->attempts >= $this->max_attempts;
    }

    public function canResend(): bool
    {
        if ($this->isVerified()) {
            return false;
        }

        if (!$this->resend_available_at) {
            return true;
        }

        return $this->resend_available_at->isPast();
    }

    public function verifyOtp(string $otp): bool
    {
        return Hash::check($otp, $this->otp_code_hash);
    }

    public function incrementAttempts(): void
    {
        $this->increment('attempts');
    }

    public function markVerified(): void
    {
        $this->update([
            'verified_at' => now(),
        ]);
    }
}
