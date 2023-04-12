<?php

if (!function_exists('proxy')) {
    function proxy(string $name): array
    {
        return [
            'url'    => env($name . '_PROXY_URL',    env('PAYMENTS_PROXY_URL')),
            'cipher' => env($name . '_PROXY_CIPHER', env('PAYMENTS_PROXY_CIPHER')),
            'key'    => env($name . '_PROXY_KEY',    env('PAYMENTS_PROXY_KEY')),
            'iv'     => env($name . '_PROXY_IV',     env('PAYMENTS_PROXY_IV')),
        ];
    }
}

return [
    'stripe_remote' => [
        'test' => boolval(env('STRIPE_REMOTE_TEST')),
        'seoAdv' => [
            'url'      => env('SA_STRIPE_REMOTE_URL'),
            'meta_key' => env('SA_STRIPE_REMOTE_META_KEY'),
        ],
        'edTech' => [
            'url'      => env('ET_STRIPE_REMOTE_URL'),
            'meta_key' => env('ET_STRIPE_REMOTE_META_KEY'),
        ]
    ],

    'stripe' => [
        'secret'      => env('STRIPE_SECRET'),
        'hook_secret' => env('STRIPE_HOOK_SECRET'),
        'proxy'       => proxy('STRIPE'),
    ],

    'connectum' => [
        'sandbox'      => env('CONNECTUM_SANDBOX', false),
        'user'         => env('CONNECTUM_USER'),
        'password'     => env('CONNECTUM_PASSWORD'),
        'key_file'     => env('CONNECTUM_KEY_FILE'),
        'key_password' => env('CONNECTUM_KEY_PASSWORD'),
    ],

    'paypal' => [
        'sandbox'   => env('PAYPAL_SANDBOX', false),
        'client_id' => env('PAYPAL_CLIENT_ID'),
        'secret'    => env('PAYPAL_SECRET'),
        'proxy'     => proxy('PAYPAL'),
    ],
    'payOp' => [
        'publicKey' => env('PAYOP_PUBLIC_KEY'),
        'secretKey' => env('PAYOP_SECRET_KEY')
    ],
    'payze' => [
        'apiKey'    => env('PAYZE_API_KEY'),
        'apiSecret' => env('PAYZE_SECRET_KEY')
    ],
    'monerchy' => [
        'merchantID' => env('MONERCHY_MERCHANT_ID'),
        'apiKeyToken' => env('MONERCHY_API_KEY_TOKEN'),
        'isDebugMode' => env('MONERCHY_DEBUG_MODE', false),
    ],
    'bepaid' => [
        'shopId' => env('BEPAID_SHOP_ID'),
        'shopSecret' => env('BEPAID_SHOP_SECRET'),
        'isTestMode' => env('BEPAID_TEST_MODE', false),
        'isDebugMode' => env('BEPAID_DEBUG_MODE', false),
    ],
    'payToDay' => [
        'shop_id' => env('PAY_TO_DAY_SHOP_ID'),
        'api_key' => env('PAY_TO_DAY_API_KEY'),
    ],

    'admin' => [
        'paymentSystems' => [
            'payop' => [
                'title' => 'PayOp',
                'isGate' => true,
                'class' => \App\PaymentSystems\PayOpPaymentSystem::class
            ],
            'youkassa' => [
                'title' => 'ЮКасса',
                'isGate' => true,
                'class'  => \App\PaymentSystems\YooPaymentSystem::class
            ],
            'paymore' => [
                'title'  => 'Paymore',
                'isGate' => false,
                'class'  => \App\PaymentSystems\PaymorePaymentSystem::class
            ],
            'cryptocloud' => [
                'title' => 'Cryptocloud',
                'isGate' => false,
                'class' => \App\PaymentSystems\CryptoCloudPaymentSystem::class
            ],
            'paypal' => [
                'title' => 'PayPal',
                'isGate' => false,
                'class' => \App\PaymentSystems\PayPalPaymentSystem::class
            ],
            'paypalRubToUsd' => [
                'title' => 'PayPal RUB to USD',
                'isGate' => false,
                'class' => \App\PaymentSystems\PayPalRubToUsdPaymentSystem::class
            ],
            'paypalRubToEur' => [
                'title' => 'PayPal RUB to EUR',
                'isGate' => false,
                'class' => \App\PaymentSystems\PayPalRubToEurPaymentSystem::class
            ],
            'stripeEdTech' => [
                'title' => 'Stripe EdTech',
                'isGate' => true,
                'class' => \App\PaymentSystems\StripeEdTechRemotePaymentSystem::class
            ],
            'stripeSeoAdv' => [
                'title' => 'Stripe SeoAdv',
                'isGate' => true,
                'class' => \App\PaymentSystems\StripeSeoAdvRemotePaymentSystem::class
            ],
            'connectum' => [
                'title'  => 'Connectum',
                'isGate' => false,
                'class'  => \App\PaymentSystems\ConnectumPaymentSystem::class
            ],
            'payze' => [
                'title'  => 'Payze',
                'isGate' => false,
                'class'  => \App\PaymentSystems\PayzePaymentSystem::class
            ],
            'monerchy' => [
                'title' => 'Monerchy',
                'isGate' => false,
                'class' => \App\PaymentSystems\MonerchyPaymentSystem::class
            ],
            'bepaid' => [
                'title' => 'BePaid',
                'isGate' => false,
                'class' => \App\PaymentSystems\BePaidPaymentSystem::class
            ],
            'paytoday' => [
                'title' => 'PayToDay',
                'isGate' => false,
                'class' => \App\PaymentSystems\PayToDayPaymentSystem::class
            ],
        ],

        'iconsBaseDir' => env('PS_ADMIN_ICON_BASE_DIR', 'payment-methods')
    ],

    'keitaroPostbackUrl' => env('KEITARO_POSTBACK_URL')
];
