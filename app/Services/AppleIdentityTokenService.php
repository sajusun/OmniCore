<?php

declare(strict_types=1);

namespace App\Services;

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use RuntimeException;
use UnexpectedValueException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

final class AppleIdentityTokenService
{
    private const APPLE_KEYS_URL    = 'https://appleid.apple.com/auth/keys';
    private const APPLE_ISSUER      = 'https://appleid.apple.com';
    private const CACHE_KEY         = 'apple_public_jwks';
    private const CACHE_TTL_SECONDS = 86400; // 24 hours


    public function verify(string $identityToken): array
    {
        $header = $this->decodeJwtHeader($identityToken);
        $kid    = $this->extractKid($header);

        $jwks = $this->fetchApplePublicKeys();

        if (!$this->hasKid($jwks, $kid)) {
            Cache::forget(self::CACHE_KEY);
            $jwks = $this->fetchApplePublicKeys();

            if (!$this->hasKid($jwks, $kid)) {
                throw new RuntimeException(
                    sprintf('No Apple public key found for kid "%s".', $kid)
                );
            }
        }

        try {
            $keys    = JWK::parseKeySet($jwks);
            $decoded = JWT::decode($identityToken, $keys);
        } catch (\Throwable $e) {
            throw new RuntimeException(
                'Apple identity token signature verification failed: ' . $e->getMessage(),
                0,
                $e
            );
        }

        $payload = (array) $decoded;

        $this->validateClaims($payload);

        return $this->normalisePayload($payload);
    }


    private function decodeJwtHeader(string $token): array
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            throw new RuntimeException('Apple identity token is malformed: expected three dot-separated segments.');
        }

        $headerJson = base64_decode(strtr($parts[0], '-_', '+/'), true);

        if ($headerJson === false) {
            throw new RuntimeException('Apple identity token header could not be base64url-decoded.');
        }

        $header = json_decode($headerJson, true);

        if (!is_array($header)) {
            throw new RuntimeException('Apple identity token header is not valid JSON.');
        }

        return $header;
    }


    private function extractKid(array $header): string
    {
        if (empty($header['kid']) || !is_string($header['kid'])) {
            throw new RuntimeException('Apple identity token header is missing a valid `kid` field.');
        }

        return $header['kid'];
    }


    private function fetchApplePublicKeys(): array
    {
        /** @var array<string, mixed>|null $cached */
        $cached = Cache::get(self::CACHE_KEY);

        if ($cached !== null) {
            return $cached;
        }

        $response = Http::timeout(10)->get(self::APPLE_KEYS_URL);

        if (!$response->successful()) {
            throw new RuntimeException(
                sprintf(
                    'Failed to fetch Apple public keys from %s (HTTP %d).',
                    self::APPLE_KEYS_URL,
                    $response->status()
                )
            );
        }

        /** @var array<string, mixed> $jwks */
        $jwks = $response->json();

        if (empty($jwks['keys']) || !is_array($jwks['keys'])) {
            throw new RuntimeException('Apple JWKS response does not contain a `keys` array.');
        }

        Cache::put(self::CACHE_KEY, $jwks, self::CACHE_TTL_SECONDS);

        Log::info('AppleIdentityTokenService: refreshed Apple public JWKS cache.');

        return $jwks;
    }


    private function hasKid(array $jwks, string $kid): bool
    {
        if (empty($jwks['keys']) || !is_array($jwks['keys'])) {
            return false;
        }

        foreach ($jwks['keys'] as $key) {
            if (isset($key['kid']) && $key['kid'] === $kid) {
                return true;
            }
        }

        return false;
    }


    private function validateClaims(array $payload): void
    {
        // --- iss ---
        if (($payload['iss'] ?? '') !== self::APPLE_ISSUER) {
            throw new UnexpectedValueException(
                sprintf(
                    'Apple identity token issuer mismatch. Expected "%s", got "%s".',
                    self::APPLE_ISSUER,
                    $payload['iss'] ?? '(missing)'
                )
            );
        }

        // --- aud ---
        $expectedAud = config('services.apple.client_id');
        $tokenAud    = $payload['aud'] ?? null;

        // `aud` may be a string or an array in Apple tokens.
        $audMatches = is_array($tokenAud)
            ? in_array($expectedAud, $tokenAud, true)
            : ($tokenAud === $expectedAud);

        if (!$audMatches) {
            throw new UnexpectedValueException(
                sprintf(
                    'Apple identity token audience mismatch. Expected "%s", got "%s".',
                    $expectedAud,
                    is_array($tokenAud) ? implode(', ', $tokenAud) : ($tokenAud ?? '(missing)')
                )
            );
        }

        // --- iat (must be in the past) ---
        if (empty($payload['iat']) || (int) $payload['iat'] > time()) {
            throw new UnexpectedValueException(
                'Apple identity token `iat` claim is missing or set in the future.'
            );
        }

        // --- exp is handled by firebase/php-jwt automatically ---
    }


    private function normalisePayload(array $payload): array
    {
        if (empty($payload['sub']) || !is_string($payload['sub'])) {
            throw new UnexpectedValueException(
                'Apple identity token is missing the `sub` (subject / Apple User ID) claim.'
            );
        }

        $emailVerifiedRaw = $payload['email_verified'] ?? false;

        // Apple may return the boolean as a JSON string "true" / "false".
        $emailVerified = filter_var($emailVerifiedRaw, FILTER_VALIDATE_BOOLEAN);

        return [
            'apple_id'       => $payload['sub'],
            'email'          => isset($payload['email']) && is_string($payload['email'])
                ? $payload['email']
                : null,
            'email_verified' => $emailVerified,
        ];
    }
}
