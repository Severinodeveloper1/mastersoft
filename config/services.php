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

    'erp' => [
        'base_url' => env('ERP_BASE_URL', 'https://api.vesertech.com/api/v1'),
        'tenant' => env('ERP_TENANT'),
        'username' => env('ERP_USERNAME'),
        'password' => env('ERP_PASSWORD'),
        'application' => env('ERP_APPLICATION', 'W'),
        'device' => env('ERP_DEVICE', 'Mastersoft sync'),
        'per_page' => (int) env('ERP_PER_PAGE', 50),
        'timeout' => (int) env('ERP_TIMEOUT', 30),
        'sync_time' => env('ERP_SYNC_TIME', '03:00'),
    ],

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

];
