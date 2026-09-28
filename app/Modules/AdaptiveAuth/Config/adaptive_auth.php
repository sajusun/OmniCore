<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Adaptive Auth Status
    |--------------------------------------------------------------------------
    | Enable or disable adaptive device & location based authentication.
    */
    'enabled' => env('ADAPTIVE_AUTH_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Device Tracking Cookie
    |--------------------------------------------------------------------------
    | The name of the HTTP-only secure cookie stored on trusted browsers.
    */
    'cookie_name' => env('ADAPTIVE_AUTH_COOKIE_NAME', 'adaptive_device_token'),

    /*
    |--------------------------------------------------------------------------
    | Device Trust Lifetime (in Days)
    |--------------------------------------------------------------------------
    | How many days a verified device remains trusted before needing re-verification.
    | Default: 60 days.
    */
    'trust_duration_days' => (int) env('ADAPTIVE_AUTH_TRUST_DAYS', 60),

    /*
    |--------------------------------------------------------------------------
    | OTP Settings
    |--------------------------------------------------------------------------
    | Length of the OTP, expiration in minutes, max failed attempts, and resend cooldown.
    */
    'otp' => [
        'length'                  => (int) env('ADAPTIVE_AUTH_OTP_LENGTH', 6),
        'expires_minutes'         => (int) env('ADAPTIVE_AUTH_OTP_EXPIRY', 10),
        'max_attempts'            => (int) env('ADAPTIVE_AUTH_MAX_ATTEMPTS', 3),
        'resend_cooldown_seconds' => (int) env('ADAPTIVE_AUTH_RESEND_COOLDOWN', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Geographic Verification Strictness
    |--------------------------------------------------------------------------
    | Supported options:
    | - 'none'      : Device cookie & browser signature only. (Good for frequent travelers)
    | - 'country'   : Flags logins if the country differs from previous trusted sessions.
    | - 'city'      : Flags logins if city or region changes significantly. (Balanced)
    | - 'strict_ip' : Flags every new IP address (High security, more frequent OTPs).
    */
    'geo_check_level' => env('ADAPTIVE_AUTH_GEO_LEVEL', 'city'),

    /*
    |--------------------------------------------------------------------------
    | Trust on First Login (Bootstrap Mode)
    |--------------------------------------------------------------------------
    | If true, a user logging in for the very first time with 0 registered devices
    | will have their current device automatically trusted without an initial challenge.
    | If false, even the first device will require email OTP confirmation.
    */
    'trust_first_login' => env('ADAPTIVE_AUTH_TRUST_FIRST_LOGIN', true),

    /*
    |--------------------------------------------------------------------------
    | Security Alerts (Email Notifications)
    |--------------------------------------------------------------------------
    | Send an email alert to the account owner whenever a new device is verified
    | and successfully accesses the account.
    */
    'notify_on_new_device' => env('ADAPTIVE_AUTH_NOTIFY_NEW_DEVICE', true),

    /*
    |--------------------------------------------------------------------------
    | Supported Authenticatable Models
    |--------------------------------------------------------------------------
    | Models supported by the adaptive auth module.
    */
    'models' => [
        'user' => App\Models\User::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Redirection after Successful Web Verification
    |--------------------------------------------------------------------------
    | Route name or fallback path to redirect user after OTP success.
    */
    'redirect_route' => 'admin.dashboard',
];
