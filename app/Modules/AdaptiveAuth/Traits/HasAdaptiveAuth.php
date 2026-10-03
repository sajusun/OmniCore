<?php

declare(strict_types=1);

namespace App\Modules\AdaptiveAuth\Traits;

use App\Modules\AdaptiveAuth\Models\DeviceLoginChallenge;
use App\Modules\AdaptiveAuth\Models\DeviceLoginLog;
use App\Modules\AdaptiveAuth\Models\UserDevice;
use App\Modules\AdaptiveAuth\Models\UserTotpCredential;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasAdaptiveAuth
{
    /**
     * Get all devices associated with this user.
     */
    public function devices(): MorphMany
    {
        return $this->morphMany(UserDevice::class, 'authenticatable');
    }

    /**
     * Get only currently trusted devices.
     */
    public function trustedDevices(): MorphMany
    {
        return $this->devices()
            ->where('is_trusted', true)
            ->whereNull('revoked_at')
            ->where(function ($q) {
                $q->whereNull('trusted_until')
                  ->orWhere('trusted_until', '>', now());
            });
    }

    /**
     * Get the user's TOTP Authenticator credential.
     */
    public function totpCredential(): MorphOne
    {
        return $this->morphOne(UserTotpCredential::class, 'authenticatable');
    }

    /**
     * Check if the user has active TOTP MFA enabled.
     */
    public function hasTotpEnabled(): bool
    {
        return $this->totpCredential()
            ->where('is_enabled', true)
            ->whereNotNull('confirmed_at')
            ->exists();
    }

    /**
     * Get all device login challenges.
     */
    public function loginChallenges(): MorphMany
    {
        return $this->morphMany(DeviceLoginChallenge::class, 'authenticatable');
    }

    /**
     * Get all device login logs.
     */
    public function loginLogs(): MorphMany
    {
        return $this->morphMany(DeviceLoginLog::class, 'authenticatable')
            ->orderByDesc('created_at');
    }

    /**
     * Check if the user has any trusted devices registered.
     */
    public function hasTrustedDevices(): bool
    {
        return $this->trustedDevices()->exists();
    }

    /**
     * Revoke a specific device by ID.
     */
    public function revokeDevice(int $deviceId): bool
    {
        $device = $this->devices()->find($deviceId);
        if ($device) {
            return $device->revoke();
        }
        return false;
    }

    /**
     * Revoke all devices except the currently used device.
     */
    public function revokeOtherDevices(?string $currentDeviceUuid = null): int
    {
        $query = $this->devices()->where('is_trusted', true);

        if ($currentDeviceUuid) {
            $query->where('device_uuid', '!=', $currentDeviceUuid);
        }

        return $query->update([
            'is_trusted' => false,
            'revoked_at' => now(),
        ]);
    }

    /**
     * Clear all login audit logs for this user.
     */
    public function clearLoginLogs(): int
    {
        return $this->loginLogs()->delete();
    }
}
