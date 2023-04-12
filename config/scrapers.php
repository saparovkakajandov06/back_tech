<?php

$emails = env('EMAIL_NOTIFICATION_TO') ? explode(',', env('EMAIL_NOTIFICATION_TO')) : [];
$to = array_pop($emails);
$cc = $emails;

return [

    'notification' => [
        'telegram' => [
            'enabled'   => env('TELEGRAM_NOTIFICATION_ENABLED', false),
            'bot_token' => env('TELEGRAM_NOTIFICATION_BOT_KEY', ''),
            'chat_ids'  => env('TELEGRAM_NOTIFICATION_CHAT_IDS') ? explode(',', env('TELEGRAM_NOTIFICATION_CHAT_IDS')) : [],
        ],
        'email' => [
            'enabled' => env('EMAIL_NOTIFICATION_ENABLED', false),
            'to' => $to,
            'cc' => $cc,
        ],
    ],

    'log_enabled' => env('SCRAPER_LOG_ENABLED', true),

    'instagram' => [
        'list_for_app' => explode(",", env('SCRAPER_IG_LIST_FOR_APP', '')),
        'list' => env('SCRAPER_IG_LIST') ? explode(',', env('SCRAPER_IG_LIST')) : [],
        'bobo' => [
            'key'  => env('SCRAPER_IGBOBO_KEY'),
            'host' => env('SCRAPER_IGBOBO_HOST'),
            'ttl'  => env('SCRAPER_IGBOBO_TTL', 180),
        ],
        '28' => [
            'key'  => env('SCRAPER_IG28_KEY'),
            'host' => env('SCRAPER_IG28_HOST'),
            'ttl'  => env('SCRAPER_IG28_TTL', 180),
        ],
        'data' => [
            'key'  => env('SCRAPER_IDATA_KEY'),
            'host' => env('SCRAPER_IDATA_HOST'),
            'ttl'  => env('SCRAPER_IDATA_TTL', 180),
        ]
    ],

    'tiktok' => [
        'list' => env('SCRAPER_TIKTOK_LIST') ? explode(',', env('SCRAPER_TIKTOK_LIST')) : [],
        'jo' => [
            'key'  => env('TIKTOK_JO_SCRAPER_KEY'),
            'host' => env('TIKTOK_JO_SCRAPER_HOST'),
            'ttl'  => env('TIKTOK_JO_SCRAPER_TTL', 180),
        ],
        'bestexperience' => [
            'key'  => env('TIKTOK_BESTEXPERIENCE_SCRAPER_KEY'),
            'host' => env('TIKTOK_BESTEXPERIENCE_SCRAPER_HOST'),
            'ttl'  => env('TIKTOK_BESTEXPERIENCE_SCRAPER_TTL', 180),
        ]
    ],

    'url_proxy_provider' => env('SCRAPER_URL_PROXY_PROVIDER', false),
];
