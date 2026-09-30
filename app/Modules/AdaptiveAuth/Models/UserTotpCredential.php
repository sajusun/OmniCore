<?php

declare(strict_types=1);

namespace App\Modules\AdaptiveAuth\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class UserTotpCredential extends Model
{
    use HasFactory;

    protected $table = 'user_totp_credentials';

    protected $fillable = [
        'authenticatable_type',
        'authenticatable_id',
        'secret_key',
        'recovery_codes',
        'is_enabled',
        'confirmed_at',
        'last_used_at',
    ];

    protected $casts = [
        'is_enabled'    => 'boolean',
        'confirmed_at'  => 'datetime',
        'last_used_at'  => 'datetime',
        'recovery_codes'=> 'array',
    ];

    /**
     * Parent authenticatable relation (User, Admin, HotelOwner).
     */
    public function authenticatable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Encrypt the TOTP secret key automatically before saving.
     */
    public function setSecretKeyAttribute(string $value): void
    {
        $this->attributes['secret_key'] = Crypt::encryptString($value);
    }

    /**
     * Decrypt the TOTP secret key automatically upon retrieval.
     */
    public function getDecryptedSecretKey(): ?string
    {
        if (empty($this->attributes['secret_key'])) {
            return null;
        }

        try {
            return Crypt::decryptString($this->attributes['secret_key']);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Verify and consume a backup recovery code.
     */
    public function useRecoveryCode(string $code): bool
    {
        $codes = $this->recovery_codes ?? [];
        $cleanedInput = strtolower(str_replace(['-', ' '], '', trim($code)));

        foreach ($codes as $index => $storedHash) {
            if (Hash::check($cleanedInput, $storedHash)) {
                // Consume code (remove it from list so it can only be used once)
                unset($codes[$index]);
                $this->recovery_codes = array_values($codes);
                $this->last_used_at = now();
                $this->save();

                return true;
            }
        }

        return false;
    }
}
