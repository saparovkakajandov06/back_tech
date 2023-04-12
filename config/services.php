<?php

if (!function_exists('parseDomainsForMeta')) {
    function parseDomainsForMeta()
    {
        $domains = [];

        foreach ($_ENV as $key => $pixelDataStr) {
            if (str_contains($key, 'META_PIXEL_')) {
                $pixelData = explode('|', $pixelDataStr);
                if (count($pixelData) > 1 && !in_array('', $pixelData)) {
                    $domain = str_replace('META_PIXEL_', '', $key);
                    $domains[$domain] = [
                        'pixel_id' => $pixelData[0],
                        'access_token' => $pixelData[1],
                        'currency' => $pixelData[2] ?? 'USD',
                        'test_event_code' => env("META_TEST_PIXEL_CODE_$domain")
                    ];
                }
            }
        }

        /*
         * Returns [
         *      "domain" => [
         *          "pixel_id" => string,
         *          "access_token" => string,
         *      ]
         * ]
         * */
        return $domains;
    }
}

if (!function_exists('parseDomainsForGA')) {
    function parseDomainsForGA()
    {
        $domains = [];

        foreach ($_ENV as $key => $pixelDataStr) {
            if (str_contains($key, 'GA_ID_')) {
                if ($val = env($key, false)) {
                    $domain = str_replace('GA_ID_', '', $key);
                    $domains[$domain] = $val;
                }
            }
        }

        /*
         * Returns [
         *      "domain" => "ga_id"
         * ]
         * */
        return $domains;
    }
}

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => env('SES_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook' => [
            'secret' => env('STRIPE_WEBHOOK_SECRET'),
            'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
        ],
    ],

    'vk_login' => [
        'client_id' => env('VKLOGIN_CLIENT_ID'),
        'secret' => env('VKLOGIN_SECRET'),
    ],

    'fb_login' => [
        'client_id' => env('FBLOGIN_CLIENT_ID'),
        'secret' => env('FBLOGIN_SECRET'),
        'redirect_uri' => env('FBLOGIN_REDIRECT_URI'),
        'force_ipv6' => env('FBLOGIN_FORCE_IPV6'),
    ],

    'metapixel' => [
        'domains' => parseDomainsForMeta(),
    ],

    'ga' => [
        'domains' => parseDomainsForGA(),
    ],
    
    'instagram_notification' => [
        'url'  => env('INSTAGRAM_NOTIFICATION_SERVICE_URL'),
        'send' => [
            'auth'  => explode(',', env('INSTAGRAM_NOTIFICATION_AUTH')),
            'guest' => explode(',', env('INSTAGRAM_NOTIFICATION_GUEST'))
        ]
    ],

    'login_stats' => [
        'enabled'  => env('LOGIN_STATS_ENABLED'),
        'spreadsheet_id' => env('LOGIN_STATS_SPREADSHEET_ID'),
        'schedule' => [
            'first_time'  => env('LOGIN_STATS_FIRST_TIME', 0),
            'second_time' => env('LOGIN_STATS_SECOND_TIME', 12),
        ],
    ],

    'sendpulse' => [
        'enabled'   => env('SENDPULSE_ENABLED'),
        'client_id' => env('SENDPULSE_CLIENT_ID'),
        'secret'    => env('SENDPULSE_CLIENT_SECRET'),
    ],
];
