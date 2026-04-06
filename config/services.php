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

    // Steadfast Courier Configuration
    'steadfast' => [
        'base_url' => env('STEADFAST_BASE_URL', 'https://portal.steadfast.com.bd'),
        'api_key' => env('STEADFAST_API_KEY'),
        'secret_key' => env('STEADFAST_SECRET_KEY'),
        'pickup_address' => env('STEADFAST_PICKUP_ADDRESS', 'Your Shop Address, Dhaka'),
    ],

    // Pathao Courier Configuration
    'pathao' => [
        'base_url' => env('PATHAO_BASE_URL', 'https://api-hermes.pathao.com'),
        'client_email' => env('PATHAO_CLIENT_EMAIL'),
        'client_password' => env('PATHAO_CLIENT_PASSWORD'),
        'client_secret' => env('PATHAO_CLIENT_SECRET'),
    ],

    // eCourier Configuration
    'ecourier' => [
        'base_url' => env('ECOURIER_BASE_URL', 'https://ecourier.com.bd'),
        'api_key' => env('ECOURIER_API_KEY'),
        'secret_key' => env('ECOURIER_SECRET_KEY'),
        'user_id' => env('ECOURIER_USER_ID'),
        'pickup_address' => env('ECOURIER_PICKUP_ADDRESS', 'Your Shop Address, Dhaka'),
    ],

    // RedX Configuration
    'redx' => [
        'base_url' => env('REDX_BASE_URL', 'https://redx.com.bd'),
        'api_key' => env('REDX_API_KEY'),
        'store_id' => env('REDX_STORE_ID'),
        'pickup_address' => env('REDX_PICKUP_ADDRESS', 'Your Shop Address, Dhaka'),
    ],

    // Paperfly Configuration
    'paperfly' => [
        'base_url' => env('PAPERFLY_BASE_URL', 'https://paperfly.com.bd'),
        'api_key' => env('PAPERFLY_API_KEY'),
        'secret_key' => env('PAPERFLY_SECRET_KEY'),
        'pickup_address' => env('PAPERFLY_PICKUP_ADDRESS', 'Your Shop Address, Dhaka'),
    ],

    // Sundarban Courier Configuration
    'sundarban' => [
        'base_url' => env('SUNDARBAN_BASE_URL', 'https://sundarban.com'),
        'api_key' => env('SUNDARBAN_API_KEY'),
        'merchant_id' => env('SUNDARBAN_MERCHANT_ID'),
        'pickup_address' => env('SUNDARBAN_PICKUP_ADDRESS', 'Your Shop Address, Dhaka'),
    ],

    // SA Paribahan Configuration
    'saparibahan' => [
        'base_url' => env('SAPARIBAHAN_BASE_URL', 'https://saparibahan.com'),
        'api_key' => env('SAPARIBAHAN_API_KEY'),
        'merchant_code' => env('SAPARIBAHAN_MERCHANT_CODE'),
        'pickup_address' => env('SAPARIBAHAN_PICKUP_ADDRESS', 'Your Shop Address, Dhaka'),
    ],

    // Janani Express Configuration
    'janani' => [
        'base_url' => env('JANANI_BASE_URL', 'https://jananiexpress.com'),
        'api_key' => env('JANANI_API_KEY'),
        'merchant_id' => env('JANANI_MERCHANT_ID'),
        'pickup_address' => env('JANANI_PICKUP_ADDRESS', 'Your Shop Address, Dhaka'),
    ],

    // Facebook Configuration
    'facebook' => [
        'pixel_id' => env('FACEBOOK_PIXEL_ID'),
        'access_token' => env('FACEBOOK_ACCESS_TOKEN'),
        'enabled' => env('FACEBOOK_ENABLED', false),
        'test_event_code' => env('FACEBOOK_TEST_EVENT_CODE', null),
    ],

];
