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
    // --------------------- CUSTOM-MADE SECRETS ---------------------
    'artisan' => [
        'dpanel_secret' => env('DEV_SECURITY_KEY'),
        'spanel_secret' => env('ADMIN_SECURITY_KEY'),
    ],
    // --------------------- CUSTOM-MADE SECRETS ---------------------

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    // --------------------- PAYMENT ---------------------
    // are loaded from app\Providers\EnvLoadServiceProvider.php
    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'stripe_currency' => env('STRIPE_CURRENCY'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'admin_fee_percentage' => env('ADMIN_FEE_PERCENTAGE') ?? 0,
    ],
    'revenuecat' => [
        'key' => env('REVENUECAT_PUBLIC_API_KEY'),
        'secret' => env('REVENUECAT_SECRET_KEY'),
        'webhook_secret' => env('REVENUECAT_AUTH_TOKEN'),
        'project_id' => env('REVENUECAT_PROJECT_ID'),
    ],
    // --------------------- AI ---------------------
    'openai' => [
        'chatgpt' => env('OPENAI_API_KEY'),
    ],

    // --------------------- SOCIAL LOGINS ---------------------
    'github' => [
        'client_id' => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        'redirect' => env('GIT_CALLBACK_URL'),
    ],
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_CALLBACK_URL'),
    ],
    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_CALLBACK_URL'),
    ],

];
