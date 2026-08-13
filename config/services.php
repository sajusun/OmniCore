<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],
        'apple' => [
        // The App ID / Service ID registered in the Apple Developer Portal.
        // Used as the `aud` claim in the identity token.
        'client_id'        => env('APPLE_CLIENT_ID'),

        // Legacy Socialite key (kept for compatibility, not used by AppleIdentityTokenService).
        'client_secret'    => env('APPLE_CLIENT_SECRET'),
        'redirect'         => env('APPLE_REDIRECT_URI'),

        // Apple Developer Portal values (needed if you later generate client secrets).
        'team_id'          => env('APPLE_TEAM_ID'),
        'key_id'           => env('APPLE_KEY_ID', '8ZNZ6UJ29L'),

        // Absolute path to the downloaded .p8 private key file.
        'private_key_path' => env(
            'APPLE_PRIVATE_KEY_PATH',
            storage_path('app/private/AuthKey_8ZNZ6UJ29L.p8')
        ),
    ],
    
    'revenuecat' => [
        'webhook_secret' => env('REVENUECAT_WEBHOOK_SECRET'),
    ],

];
