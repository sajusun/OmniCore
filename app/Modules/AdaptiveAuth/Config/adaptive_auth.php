<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Adaptive Auth Status
    |--------------------------------------------------------------------------
    */
    'enabled' => env('ADAPTIVE_AUTH_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Device Tracking Cookie
    |--------------------------------------------------------------------------
    | The name of the HTTP-only secure signed cookie stored on trusted browsers.
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
    | Role-Based MFA & Adaptive Policies
    |--------------------------------------------------------------------------
    | Define authentication security levels per user role.
    | - totp_mandatory : Password + Authenticator App (Mandatory for Admin/Support)
    | - adaptive_otp   : Password + Adaptive Device Check + Email OTP on new/risky device
    */
    'role_policies' => [
        'admin'       => [
            'mode'          => 'totp_mandatory',
            'totp_required' => true,
        ],
        'support'     => [
            'mode'          => 'totp_mandatory',
            'totp_required' => true,
        ],
        'hotel_owner' => [
            'mode'          => 'adaptive_otp',
            'totp_optional' => true,
        ],
        'customer'    => [
            'mode'          => 'adaptive_otp',
            'totp_optional' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | OTP Settings
    |--------------------------------------------------------------------------
    */
    'otp' => [
        'length'                  => (int) env('ADAPTIVE_AUTH_OTP_LENGTH', 6),
        'expires_minutes'         => (int) env('ADAPTIVE_AUTH_OTP_EXPIRY', 10),
        'max_attempts'            => (int) env('ADAPTIVE_AUTH_MAX_ATTEMPTS', 3),
        'resend_cooldown_seconds' => (int) env('ADAPTIVE_AUTH_RESEND_COOLDOWN', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Step-Up Authentication (Sensitive Action Protection)
    |--------------------------------------------------------------------------
    | Require password/MFA re-confirmation for high-risk actions (e.g. Bank details,
    | Payout settings, Security/MFA changes) within a 15-minute sliding window.
    */
    'step_up' => [
        'timeout_minutes' => (int) env('ADAPTIVE_AUTH_STEP_UP_TIMEOUT', 15),
        'confirm_route'   => 'password.confirm',
    ],

    /*
    |--------------------------------------------------------------------------
    | Geographic Verification Strictness
    |--------------------------------------------------------------------------
    | Supported options:
    | - 'none'      : Device cookie & signature only.
    | - 'country'   : Flags logins if the country differs from previous trusted sessions.
    | - 'city'      : Flags logins if city or region changes significantly.
    | - 'strict_ip' : Flags every new IP address.
    */
    'geo_check_level' => env('ADAPTIVE_AUTH_GEO_LEVEL', 'country'),

    /*
    |--------------------------------------------------------------------------
    | Trust on First Login (Bootstrap Mode)
    |--------------------------------------------------------------------------
    */
    'trust_first_login' => env('ADAPTIVE_AUTH_TRUST_FIRST_LOGIN', false),

    /*
    |--------------------------------------------------------------------------
    | Security Alerts (Email Notifications)
    |--------------------------------------------------------------------------
    */
    'notify_on_new_device' => env('ADAPTIVE_AUTH_NOTIFY_NEW_DEVICE', true),

    /*
    |--------------------------------------------------------------------------
    | Default Redirection after Successful Web Verification
    |--------------------------------------------------------------------------
    */
    'redirect_route' => 'admin.dashboard',
];
