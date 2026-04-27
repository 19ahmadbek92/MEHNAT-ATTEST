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

    // Real integratsiya uchun kerakli parametrlar (OneID / E-IMZO)
    'oneid' => [
        'client_id' => env('ONEID_CLIENT_ID'),
        'client_secret' => env('ONEID_CLIENT_SECRET'),
        'redirect_uri' => env('ONEID_REDIRECT_URI'),
        'base_url' => env('ONEID_BASE_URL'),
        'authorize_path' => env('ONEID_AUTHORIZE_PATH', '/oauth2/authorize'),
        'token_path' => env('ONEID_TOKEN_PATH', '/oauth2/token'),
        'userinfo_path' => env('ONEID_USERINFO_PATH', '/oauth2/userinfo'),
        'token_auth' => env('ONEID_TOKEN_AUTH', 'body'),
        'scope' => env('ONEID_SCOPE', 'openid profile'),
        'claim_sub' => env('ONEID_CLAIM_SUB', 'sub'),
        'claim_pinfl' => env('ONEID_CLAIM_PINFL', 'pinfl'),
        'claim_name' => env('ONEID_CLAIM_NAME', 'name'),
        'claim_email' => env('ONEID_CLAIM_EMAIL', 'email'),
    ],

    'eri' => [
        'base_url' => env('ERI_BASE_URL'),
        'client_id' => env('ERI_CLIENT_ID'),
        'client_secret' => env('ERI_CLIENT_SECRET'),
        'verification_url' => env('ERI_VERIFICATION_URL'),
        'verification_timeout' => (int) env('ERI_VERIFICATION_TIMEOUT', 30),
        'pkcs7_noverify' => filter_var(env('ERI_PKCS7_NOVERIFY', false), FILTER_VALIDATE_BOOLEAN),
    ],

];
