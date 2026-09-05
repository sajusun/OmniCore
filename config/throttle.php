<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Rate Limiting Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the maximum number of requests allowed per minute for different
    | API tiers. These values can be customized via environment variables.
    |
    */

    'api' => (int) env('API_RATE_LIMIT', 60),

    'auth' => (int) env('AUTH_RATE_LIMIT', 6),

    'resend_otp' => (int) env('OTP_RATE_LIMIT', 3),
];
