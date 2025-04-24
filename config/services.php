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

    'ollama' => [
        'url' => env('OLLAMA_API_URL', 'http://localhost:11434'),
        'default_model' => env('OLLAMA_DEFAULT_MODEL', 'llama2'),
        'max_queue_size' => env('OLLAMA_MAX_QUEUE_SIZE', 100),
        'queue_timeout' => env('OLLAMA_QUEUE_TIMEOUT', 60), // seconds
        'require_api_key' => env('OLLAMA_REQUIRE_API_KEY', true),
        'api_keys' => array_filter(explode(',', env('OLLAMA_API_KEYS', ''))),
    ],

];
