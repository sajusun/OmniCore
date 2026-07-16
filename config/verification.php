<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default Verification Type
    |--------------------------------------------------------------------------
    | Supported: "otp", "token"
    */
    'default_type' => env('VERIFICATION_DEFAULT_TYPE', 'otp'),

    /*
    |--------------------------------------------------------------------------
    | OTP Settings
    |--------------------------------------------------------------------------
    */
    'otp_digits'          => (int) env('VERIFICATION_OTP_DIGITS', 6),
    'otp_expiry_minutes'  => (int) env('VERIFICATION_OTP_EXPIRY_MINUTES', 10),

    /*
    |--------------------------------------------------------------------------
    | Token Settings
    |--------------------------------------------------------------------------
    */
    'token_length'         => (int) env('VERIFICATION_TOKEN_LENGTH', 64),
    'token_expiry_minutes' => (int) env('VERIFICATION_TOKEN_EXPIRY_MINUTES', 60),

    /*
    |--------------------------------------------------------------------------
    | Attempt Limits
    |--------------------------------------------------------------------------
    */
    'max_attempts' => (int) env('VERIFICATION_MAX_ATTEMPTS', 5),

    /*
    |--------------------------------------------------------------------------
    | Resend Rules
    |--------------------------------------------------------------------------
    */
    'resend_cooldown_seconds' => (int) env('VERIFICATION_RESEND_COOLDOWN_SECONDS', 60),
    'max_resend_requests'     => (int) env('VERIFICATION_MAX_RESEND_REQUESTS', 5),

    /*
    |--------------------------------------------------------------------------
    | Block Duration
    |--------------------------------------------------------------------------
    | How many hours a user is blocked after hitting max attempts or
    | max resend requests.
    */
    'block_hours' => (int) env('VERIFICATION_BLOCK_HOURS', 24),

    /*
    |--------------------------------------------------------------------------
    | Redirect URLs (used by token verification)
    |--------------------------------------------------------------------------
    */
    'success_redirect_url' => env('VERIFICATION_SUCCESS_REDIRECT_URL', '/verification/success'),
    'failed_redirect_url'  => env('VERIFICATION_FAILED_REDIRECT_URL', '/verification/failed'),

];
