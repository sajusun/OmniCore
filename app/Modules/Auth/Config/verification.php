<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Verification Settings
    |--------------------------------------------------------------------------
    |
    | Supported types: 'otp', 'token'
    | Supported channels: 'email', 'sms', 'log'
    |
    */

    'default_type'    => env('VERIFICATION_DEFAULT_TYPE', 'otp'),
    'default_channel' => env('VERIFICATION_DEFAULT_CHANNEL', 'email'),

    'otp' => [
        'length'       => (int) env('VERIFICATION_OTP_LENGTH', 4),
        'expires_in'   => (int) env('VERIFICATION_OTP_EXPIRES_IN', 10), // in minutes
        'max_attempts' => (int) env('VERIFICATION_OTP_MAX_ATTEMPTS', 5),
    ],

    'token' => [
        'length'     => 64,
        'expires_in' => (int) env('VERIFICATION_TOKEN_EXPIRES_IN', 60), // in minutes
    ],

    'rate_limiting' => [
        'cooldown_seconds' => (int) env('VERIFICATION_COOLDOWN_SECONDS', 60),
        'max_requests'     => (int) env('VERIFICATION_MAX_REQUESTS', 5),
        'block_duration'   => (int) env('VERIFICATION_BLOCK_DURATION', 30), // in minutes
    ],

    'redirects' => [
        'success' => env('VERIFICATION_SUCCESS_URL', '/verification/success'),
        'failed'  => env('VERIFICATION_FAILED_URL', '/verification/failed'),
    ],
];
