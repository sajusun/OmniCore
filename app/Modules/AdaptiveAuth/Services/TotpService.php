<?php

declare(strict_types=1);

namespace App\Modules\AdaptiveAuth\Services;

use App\Modules\AdaptiveAuth\Models\UserTotpCredential;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class TotpService
{
    protected Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
        $this->google2fa->setWindow(1); // Allow 1 step (30s) grace period for minor clock drift
    }

    /**
     * Generate a new base32 secret key.
     */
    public function generateSecretKey(): string
    {
        return $this->google2fa->generateSecretKey(32);
    }

    /**
     * Get the OTP Auth URI for QR code scanning.
     */
    public function getOtpAuthUrl(Model $user, string $secretKey, ?string $company = null): string
    {
        $companyName = $company ?? config('app.name', 'MyBergo');
        $userIdentifier = $user->email ?? $user->username ?? (string) $user->getKey();

        return $this->google2fa->getQRCodeUrl(
            $companyName,
            $userIdentifier,
            $secretKey
        );
    }

    /**
     * Generate inline QR code SVG or Base64 Image.
     */
    public function getQrCodeSvg(Model $user, string $secretKey, ?string $company = null): string
    {
        $otpUrl = $this->getOtpAuthUrl($user, $secretKey, $company);

        if (class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
            return (string) \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)
                ->format('svg')
                ->generate($otpUrl);
        }

        // Fallback standard Google Chart API image URL (or inline SVG)
        $encodedUrl = urlencode($otpUrl);
        return "<img src=\"https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={$encodedUrl}\" alt=\"QR Code\" class=\"mx-auto\" />";
    }

    /**
     * Verify a 6-digit TOTP code against a secret key.
     */
    public function verifyCode(string $secretKey, string $code): bool
    {
        $cleanCode = preg_replace('/\s+/', '', $code);
        if (strlen($cleanCode) !== 6 || !ctype_digit($cleanCode)) {
            return false;
        }

        return (bool) $this->google2fa->verifyKey($secretKey, $cleanCode);
    }

    /**
     * Generate 8 secure random backup recovery codes.
     * Returns: ['plain' => [...], 'hashed' => [...]]
     */
    public function generateRecoveryCodes(int $count = 8): array
    {
        $plain = [];
        $hashed = [];

        for ($i = 0; $i < $count; $i++) {
            $code = strtoupper(Str::random(5) . '-' . Str::random(5));
            $plain[] = $code;
            $cleaned = strtolower(str_replace('-', '', $code));
            $hashed[] = Hash::make($cleaned);
        }

        return [
            'plain'  => $plain,
            'hashed' => $hashed,
        ];
    }

    /**
     * Confirm and activate TOTP for a user.
     */
    public function enableTotp(Model $user, string $secretKey, string $code): array
    {
        if (!$this->verifyCode($secretKey, $code)) {
            return [
                'success' => false,
                'message' => 'Invalid verification code. Please check your authenticator app and try again.',
            ];
        }

        $recoveryCodes = $this->generateRecoveryCodes();

        UserTotpCredential::updateOrCreate(
            [
                'authenticatable_type' => $user->getMorphClass(),
                'authenticatable_id'   => $user->getKey(),
            ],
            [
                'secret_key'              => $secretKey,
                'recovery_codes'          => $recoveryCodes['hashed'],
                'is_enabled'              => true,
                'always_require_on_login' => true,
                'confirmed_at'            => now(),
                'last_used_at'            => now(),
            ]
        );

        return [
            'success'        => true,
            'recovery_codes' => $recoveryCodes['plain'],
            'message'        => 'Two-factor authenticator app enabled successfully.',
        ];
    }

    /**
     * Disable TOTP for a user.
     */
    public function disableTotp(Model $user): bool
    {
        $credential = UserTotpCredential::where('authenticatable_type', $user->getMorphClass())
            ->where('authenticatable_id', $user->getKey())
            ->first();

        if ($credential) {
            $credential->delete();
            return true;
        }

        return false;
    }

    /**
     * Regenerate new recovery codes for user with active TOTP.
     */
    public function regenerateRecoveryCodes(Model $user, int $count = 8): ?array
    {
        $credential = $this->getUserCredential($user);
        if (!$credential) {
            return null;
        }

        $recovery = $this->generateRecoveryCodes($count);
        $credential->update([
            'recovery_codes' => $recovery['hashed'],
        ]);

        return $recovery['plain'];
    }

    /**
     * Get the count of remaining unused recovery codes.
     */
    public function getRemainingRecoveryCodesCount(Model $user): int
    {
        $credential = $this->getUserCredential($user);
        if (!$credential || !is_array($credential->recovery_codes)) {
            return 0;
        }

        return count($credential->recovery_codes);
    }

    /**
     * Check if a user has active TOTP MFA enabled.
     */
    public function hasTotpEnabled(Model $user): bool
    {
        return UserTotpCredential::where('authenticatable_type', $user->getMorphClass())
            ->where('authenticatable_id', $user->getKey())
            ->where('is_enabled', true)
            ->whereNotNull('confirmed_at')
            ->exists();
    }

    /**
     * Verify a login attempt using either 6-digit TOTP code or a 10-char recovery code.
     */
    public function verifyUserTotpOrRecovery(Model $user, string $code): bool
    {
        $credential = UserTotpCredential::where('authenticatable_type', $user->getMorphClass())
            ->where('authenticatable_id', $user->getKey())
            ->where('is_enabled', true)
            ->first();

        if (!$credential) {
            return false;
        }

        $cleanInput = trim($code);

        // Case A: 6-digit TOTP code
        if (strlen(preg_replace('/\s+/', '', $cleanInput)) === 6 && ctype_digit(preg_replace('/\s+/', '', $cleanInput))) {
            $secretKey = $credential->getDecryptedSecretKey();
            if ($secretKey && $this->verifyCode($secretKey, $cleanInput)) {
                $credential->update(['last_used_at' => now()]);
                return true;
            }
            return false;
        }

        // Case B: Backup recovery code
        return $credential->useRecoveryCode($cleanInput);
    }

    /**
     * Get user's active TOTP credential.
     */
    public function getUserCredential(Model $user): ?UserTotpCredential
    {
        return UserTotpCredential::where('authenticatable_type', $user->getMorphClass())
            ->where('authenticatable_id', $user->getKey())
            ->where('is_enabled', true)
            ->first();
    }

    /**
     * Update user's TOTP login enforcement preference.
     */
    public function updateLoginPreference(Model $user, bool $alwaysRequire): bool
    {
        $credential = $this->getUserCredential($user);
        if ($credential) {
            return (bool) $credential->update(['always_require_on_login' => $alwaysRequire]);
        }

        return false;
    }

    /**
     * Check if user requires TOTP on every login even from recognized devices.
     */
    public function alwaysRequiresTotpOnLogin(Model $user): bool
    {
        $credential = $this->getUserCredential($user);
        if ($credential && $credential->always_require_on_login !== null) {
            return (bool) $credential->always_require_on_login;
        }

        return (bool) config('adaptive_auth.always_require_totp_on_login', false);
    }
}
