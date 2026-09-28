<?php

declare(strict_types=1);

namespace App\Modules\AdaptiveAuth\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class UserDevice extends Model
{
    use HasFactory;

    protected $table = 'user_devices';

    protected $fillable = [
        'authenticatable_type',
        'authenticatable_id',
        'device_uuid',
        'device_name',
        'platform',
        'browser',
        'device_type',
        'fingerprint_hash',
        'last_ip',
        'city',
        'region',
        'country',
        'country_code',
        'is_trusted',
        'trusted_at',
        'trusted_until',
        'last_active_at',
        'revoked_at',
    ];

    protected $casts = [
        'is_trusted'     => 'boolean',
        'trusted_at'     => 'datetime',
        'trusted_until'  => 'datetime',
        'last_active_at' => 'datetime',
        'revoked_at'     => 'datetime',
    ];

    public function authenticatable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeTrusted(Builder $query): Builder
    {
        return $query->where('is_trusted', true)
            ->whereNull('revoked_at')
            ->where(function ($q) {
                $q->whereNull('trusted_until')
                  ->orWhere('trusted_until', '>', now());
            });
    }

    public function isCurrentlyTrusted(): bool
    {
        if (!$this->is_trusted || $this->revoked_at !== null) {
            return false;
        }

        if ($this->trusted_until && $this->trusted_until->isPast()) {
            return false;
        }

        return true;
    }

    public function revoke(): bool
    {
        return $this->update([
            'is_trusted' => false,
            'revoked_at' => now(),
        ]);
    }

    public function touchActivity(string $ip, ?string $city = null, ?string $country = null): void
    {
        $updates = [
            'last_active_at' => now(),
            'last_ip'        => $ip,
        ];

        if ($city) {
            $updates['city'] = $city;
        }
        if ($country) {
            $updates['country'] = $country;
        }

        $this->update($updates);
    }
}
