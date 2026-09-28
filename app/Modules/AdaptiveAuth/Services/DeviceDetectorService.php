<?php

declare(strict_types=1);

namespace App\Modules\AdaptiveAuth\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DeviceDetectorService
{
    /**
     * Inspect incoming request and return parsed device metadata.
     *
     * @param Request $request
     * @return array
     */
    public function inspect(Request $request): array
    {
        $userAgent  = (string) $request->header('User-Agent', 'Unknown Browser');
        $ip         = $this->resolveClientIp($request);
        $platform   = $this->detectPlatform($userAgent);
        $browser    = $this->detectBrowser($userAgent);
        $deviceType = $this->detectDeviceType($userAgent);
        $deviceName = "{$browser['name']} on {$platform}";
        $location   = $this->resolveLocation($ip);

        // Secondary digital fingerprint (User-Agent + Languages)
        $entropy = $userAgent . '|' . (string) $request->header('Accept-Language', '');
        $fingerprintHash = hash('sha256', $entropy);

        // Retrieve existing device UUID from encrypted cookie or generate a new one
        $cookieName = config('adaptive_auth.cookie_name', 'adaptive_device_token');
        $deviceUuid = $request->cookie($cookieName);

        if (empty($deviceUuid) || !is_string($deviceUuid) || strlen($deviceUuid) < 16) {
            $deviceUuid = (string) Str::uuid();
            $isNewCookie = true;
        } else {
            $isNewCookie = false;
        }

        return [
            'device_uuid'      => $deviceUuid,
            'is_new_cookie'    => $isNewCookie,
            'device_name'      => $deviceName,
            'platform'         => $platform,
            'browser'          => $browser['name'],
            'browser_version'  => $browser['version'],
            'device_type'      => $deviceType,
            'fingerprint_hash' => $fingerprintHash,
            'ip'               => $ip,
            'city'             => $location['city'] ?? null,
            'region'           => $location['region'] ?? null,
            'country'          => $location['country'] ?? null,
            'country_code'     => $location['country_code'] ?? null,
            'user_agent'       => $userAgent,
        ];
    }

    /**
     * Extract accurate client IP respecting trusted proxy/Cloudflare headers.
     */
    public function resolveClientIp(Request $request): string
    {
        $headers = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_REAL_IP',
            'HTTP_X_FORWARDED_FOR',
        ];

        foreach ($headers as $header) {
            $value = $request->server($header);
            if (!empty($value)) {
                $ips = explode(',', (string) $value);
                $ip  = trim($ips[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return (string) $request->ip();
    }

    /**
     * Resolve City and Country from IP with caching and fast timeout.
     */
    public function resolveLocation(string $ip): array
    {
        if ($this->isPrivateIp($ip)) {
            return [
                'city'         => 'Localhost',
                'region'       => 'Development',
                'country'      => 'Local Network',
                'country_code' => 'LOCAL',
            ];
        }

        // Check cache for 24 hours to prevent repeated external calls
        $cacheKey = "geo_ip_" . md5($ip);
        return Cache::remember($cacheKey, 86400, function () use ($ip) {
            try {
                $response = Http::timeout(2)
                    ->get("http://ip-api.com/json/{$ip}?fields=status,country,countryCode,regionName,city");

                if ($response->successful()) {
                    $data = $response->json();
                    if (($data['status'] ?? '') === 'success') {
                        return [
                            'city'         => $data['city'] ?? null,
                            'region'       => $data['regionName'] ?? null,
                            'country'      => $data['country'] ?? null,
                            'country_code' => $data['countryCode'] ?? null,
                        ];
                    }
                }
            } catch (\Throwable $e) {
                // Fail-safe: Location lookup failure should never interrupt login flow
            }

            return [
                'city'         => null,
                'region'       => null,
                'country'      => null,
                'country_code' => null,
            ];
        });
    }

    /**
     * Detect Operating System / Platform.
     */
    protected function detectPlatform(string $userAgent): string
    {
        $platforms = [
            'iOS'        => '/iphone|ipad|ipod/i',
            'Android'    => '/android/i',
            'Windows 11' => '/windows nt 10\.0.*(windows.*arm|rv:1[1-9]|trident)/i',
            'Windows 10' => '/windows nt 10\.0/i',
            'Windows 8'  => '/windows nt 6\.[23]/i',
            'Windows 7'  => '/windows nt 6\.1/i',
            'macOS'      => '/macintosh|mac os x/i',
            'Linux'      => '/linux/i',
            'Ubuntu'     => '/ubuntu/i',
            'ChromeOS'   => '/cros/i',
        ];

        foreach ($platforms as $platform => $pattern) {
            if (preg_match($pattern, $userAgent)) {
                return $platform;
            }
        }

        return 'Unknown OS';
    }

    /**
     * Detect Browser and major version.
     */
    protected function detectBrowser(string $userAgent): array
    {
        $browsers = [
            'Edge'    => '/edg[e]?\/([0-9.]+)/i',
            'Opera'   => '/opr\/([0-9.]+)/i',
            'Chrome'  => '/(?:chrome|crios)\/([0-9.]+)/i',
            'Firefox' => '/(?:firefox|fxios)\/([0-9.]+)/i',
            'Safari'  => '/version\/([0-9.]+).*safari/i',
        ];

        foreach ($browsers as $browser => $pattern) {
            if (preg_match($pattern, $userAgent, $matches)) {
                return [
                    'name'    => $browser,
                    'version' => explode('.', $matches[1])[0] ?? '1.0',
                ];
            }
        }

        return [
            'name'    => 'Browser',
            'version' => '1.0',
        ];
    }

    /**
     * Detect device form factor (desktop, mobile, tablet).
     */
    protected function detectDeviceType(string $userAgent): string
    {
        if (preg_match('/ipad|tablet|(android(?!.*mobile))/i', $userAgent)) {
            return 'tablet';
        }

        if (preg_match('/mobile|iphone|ipod|android|blackberry|opera mini|iemobile/i', $userAgent)) {
            return 'mobile';
        }

        return 'desktop';
    }

    /**
     * Check if IP address is local / internal.
     */
    protected function isPrivateIp(string $ip): bool
    {
        return !filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }
}
