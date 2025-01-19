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

    'rabbitmq' => [
        'host' => env('RABBITMQ_HOST', 'seller_tray_rabbitmq'),
        'port' => env('RABBITMQ_PORT', '5672'),
        'user' => env('RABBITMQ_USER', 'user'),
        'password' => env('RABBITMQ_PASSWORD', 'password'),
        'queue' => env('RABBITMQ_QUEUE', 'sale_queue'),
        'exchange' => env('RABBITMQ_EXCHANGE', 'sale_exchange'),
        'bind' => env('RABBITMQ_BIND', 'sale_bind'),
        'vhost' => env('RABBITMQ_VHOST', '/'),
        'email_report_sales' => env('RABBITMQ_EMAIL_REPORT_SALES', '/'),
    ],

];
