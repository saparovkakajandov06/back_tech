<?php

namespace Database\Seeders;

use App\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodsSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            [
                'id' => [ 'id' => 1 ],
                'data' => [
                    'order'         => 1,
                    'icon'          => 'cardRF.svg',
                    'titles'        => [
                        'ru' => 'Банковская карта',
                        'en' => 'Credit card',
                        'de' => 'Kreditkarte',
                        'es' => 'Tarjeta de crédito',
                        'pt' => 'Cartão de crédito',
                        'it' => 'Carta di credito',
                        'tr' => 'Kredi kartı',
                        'uk' => 'Банківська картка'
                    ],
                    'currencies'    => ['RUB'],
                    'limits'        => [
                        'RUB' => ['min' => 10, 'max' => 75000],
                    ],
                    'countries'     => ['RU'],
                    'payment_system' => 'paymore',
                    'gate_method_id'  => null,
                    'country_filter' => 'everyExcept',
                    'show_agreement_flag'      => true
                ]
            ],
            [
                'id' => [ 'id' => 2 ],
                'data' => [
                    'order'         => 2,
                    'icon'          => 'cardRF.svg',
                    'titles'        => [
                        'ru' => 'Банковская карта',
                        'en' => 'Credit card',
                        'de' => 'Kreditkarte',
                        'es' => 'Tarjeta de crédito',
                        'pt' => 'Cartão de crédito',
                        'it' => 'Carta di credito',
                        'tr' => 'Kredi kartı',
                        'uk' => 'Банківська картка'
                    ],
                    'currencies'    => ['RUB'],
                    'limits'        => [
                        'RUB' => ['min' => 1, 'max' => 250000],
                    ],
                    'countries'     => ['RU'],
                    'payment_system' => 'youkassa',
                    'gate_method_id'  => 'bankcard',
                    'country_filter' => 'onlyAt',
                    'show_agreement_flag' => true
                ]
            ],
            [
                'id' => [ 'id' => 3 ],
                'data' => [
                    'order'         => 3,
                    'icon'          => 'sberPay.svg',
                    'titles'        => [
                        'ru' => 'SberPay',
                        'en' => 'SberPay',
                        'de' => 'SberPay',
                        'es' => 'SberPay',
                        'pt' => 'SberPay',
                        'it' => 'SberPay',
                        'tr' => 'SberPay',
                        'uk' => 'SberPay'
                    ],
                    'currencies'    => ['RUB'],
                    'limits'        => [
                        'RUB' => ['min' => 1, 'max' => 1000000],
                    ],
                    'countries'     => [],
                    'payment_system' => 'youkassa',
                    'gate_method_id'  => 'sberpay',
                    'country_filter' => 'every',
                    'show_agreement_flag' => true
                ]
            ],
            [
                'id' => [ 'id' => 4 ],
                'data' => [
                    'order'         => 4,
                    'icon'          => 'eWallet.svg',
                    'titles'        => [
                        'ru' => 'ЮMoney',
                        'en' => 'YooMoney',
                        'de' => 'YooMoney',
                        'es' => 'YooMoney',
                        'pt' => 'YooMoney',
                        'it' => 'YooMoney',
                        'tr' => 'YooMoney',
                        'uk' => 'YooMoney'
                    ],
                    'currencies'    => ['RUB'],
                    'limits'        => [
                        'RUB' => ['min' => 1, 'max' => 250000],
                    ],
                    'countries'     => [],
                    'payment_system' => 'youkassa',
                    'gate_method_id'  => 'wallet',
                    'country_filter' => 'every',
                    'show_agreement_flag' => true
                ]
            ],
            [
                'id' => [ 'id' => 5 ],
                'data' => [
                    'order'         => 5,
                    'icon'          => 'cash.svg',
                    'titles'        => [
                        'ru' => 'Наличные',
                        'en' => 'Cash',
                        'de' => 'Cash',
                        'es' => 'Cash',
                        'pt' => 'Cash',
                        'it' => 'Cash',
                        'tr' => 'Cash',
                        'uk' => 'Cash'
                    ],
                    'currencies'    => ['RUB'],
                    'limits'        => [
                        'RUB' => ['min' => 10, 'max' => 60000],
                    ],
                    'countries'     => [],
                    'payment_system' => 'youkassa',
                    'gate_method_id'  => 'cash',
                    'country_filter' => 'every',
                    'show_agreement_flag' => true
                ]
            ],
            [
                'id' => [ 'id' => 6 ],
                'data' => [
                    'order'         => 6,
                    'icon'          => 'qiwi.svg',
                    'titles'        => [
                        'ru' => 'Qiwi',
                        'en' => 'Qiwi',
                        'de' => 'Qiwi',
                        'es' => 'Qiwi',
                        'pt' => 'Qiwi',
                        'it' => 'Qiwi',
                        'tr' => 'Qiwi',
                        'uk' => 'Qiwi'
                    ],
                    'currencies'    => ['RUB'],
                    'limits'        => [
                        'RUB' => ['min' => 1, 'max' => 250000],
                    ],
                    'countries'     => [],
                    'payment_system' => 'youkassa',
                    'gate_method_id'  => 'QW',
                    'country_filter' => 'every',
                    'show_agreement_flag' => true
                ]
            ],
            [
                'id' => [ 'id' => 7 ],
                'data' => [
                    'order'         => 100,
                    'icon'          => 'crypto.svg',
                    'titles'        => [
                        'ru' => 'Криптовалюта',
                        'en' => 'Crypto',
                        'de' => 'Crypto',
                        'es' => 'Crypto',
                        'pt' => 'Crypto',
                        'it' => 'Crypto',
                        'tr' => 'Crypto',
                        'uk' => 'Crypto'
                    ],
                    'currencies'    => ['RUB', 'EUR', 'USD'],
                    'limits'        => [
                        'RUB' => ['min' => 30, 'max' => 60000000],
                        'EUR' => ['min' => 0.5, 'max' => 999999],
                        'USD' => ['min' => 0.5, 'max' => 999999],
                    ],
                    'countries'     => [],
                    'payment_system' => 'cryptocloud',
                    'gate_method_id'  => null,
                    'country_filter' => 'every',
                    'show_agreement_flag' => true
                ]
            ],
            [
                'id' => [ 'id' => 8 ],
                'data' => [
                    'order'         => 8,
                    'icon'          => 'cardRF.svg',
                    'titles'        => [
                        'ru' => 'Банковская карта (Stripe)',
                        'en' => 'Credit card (Stripe)',
                        'de' => 'Credit card (Stripe)',
                        'es' => 'Credit card (Stripe)',
                        'pt' => 'Credit card (Stripe)',
                        'it' => 'Credit card (Stripe)',
                        'tr' => 'Credit card (Stripe)',
                        'uk' => 'Credit card (Stripe)'
                    ],
                    'currencies'    => ['EUR', 'USD'],
                    'limits'        => [
                        'EUR' => ['min' => 0.5, 'max' => 999999],
                        'USD' => ['min' => 0.5, 'max' => 999999],
                    ],
                    'countries'     => [],
                    'payment_system' => 'stripeEdTech',
                    'gate_method_id'  => 'card',
                    'country_filter' => 'every',
                    'show_agreement_flag' => true
                ]
            ],
            [
                'id' => [ 'id' => 9 ],
                'data' => [
                    'order'         => 9,
                    'icon'          => 'cardRF.svg',
                    'titles'        => [
                        'ru' => 'Банковская карта (Paymore)',
                        'en' => 'Credit card (Paymore)',
                        'de' => 'Credit card (Paymore)',
                        'es' => 'Credit card (Paymore)',
                        'pt' => 'Credit card (Paymore)',
                        'it' => 'Credit card (Paymore)',
                        'tr' => 'Credit card (Paymore)',
                        'uk' => 'Credit card (Paymore)'
                    ],
                    'currencies'    => ['EUR', 'USD'],
                    'limits'        => [
                        'EUR' => ['min' => 0.3, 'max' => 1100],
                        'USD' => ['min' => 0.3, 'max' => 1100],
                    ],
                    'countries'     => [],
                    'payment_system' => 'paymore',
                    'gate_method_id'  => null,
                    'country_filter' => 'every',
                    'show_agreement_flag' => true
                ]
            ],
            [
                'id' => [ 'id' => 10 ],
                'data' => [
                    'order'         => 10,
                    'icon'          => 'applePay.svg',
                    'titles'        => [
                        'ru' => 'Apple Pay',
                        'en' => 'Apple Pay',
                        'de' => 'Apple Pay',
                        'es' => 'Apple Pay',
                        'pt' => 'Apple Pay',
                        'it' => 'Apple Pay',
                        'tr' => 'Apple Pay',
                        'uk' => 'Apple Pay'
                    ],
                    'currencies'    => ['EUR', 'USD'],
                    'limits'        => [
                        'EUR' => ['min' => 0.5, 'max' => 999999],
                        'USD' => ['min' => 0.5, 'max' => 999999],
                    ],
                    'countries'     => [],
                    'payment_system' => 'stripeEdTech',
                    'gate_method_id'  => 'card',
                    'country_filter' => 'every',
                    'show_agreement_flag' => true
                ]
            ],
            [
                'id' => [ 'id' => 11 ],
                'data' => [
                    'order'         => 11,
                    'icon'          => 'googlePay.svg',
                    'titles'        => [
                        'ru' => 'Google Pay',
                        'en' => 'Google Pay',
                        'de' => 'Google Pay',
                        'es' => 'Google Pay',
                        'pt' => 'Google Pay',
                        'it' => 'Google Pay',
                        'tr' => 'Google Pay',
                        'uk' => 'Google Pay'
                    ],
                    'currencies'    => ['EUR', 'USD'],
                    'limits'        => [
                        'EUR' => ['min' => 0.5, 'max' => 999999],
                        'USD' => ['min' => 0.5, 'max' => 999999],
                    ],
                    'countries'     => [],
                    'payment_system' => 'stripeEdTech',
                    'gate_method_id'  => 'card',
                    'country_filter' => 'every',
                    'show_agreement_flag' => true
                ]
            ],
            [
                'id' => [ 'id' => 12 ],
                'data' => [
                    'order'         => 12,
                    'icon'          => 'payPal.svg',
                    'titles'        => [
                        'ru' => 'PayPal',
                        'en' => 'PayPal',
                        'de' => 'PayPal',
                        'es' => 'PayPal',
                        'pt' => 'PayPal',
                        'it' => 'PayPal',
                        'tr' => 'PayPal',
                        'uk' => 'PayPal'
                    ],
                    'currencies'    => ['EUR', 'USD'],
                    'limits'        => [
                        'EUR' => ['min' => 0.25, 'max' => 25000],
                        'USD' => ['min' => 0.25, 'max' => 25000],
                    ],
                    'countries'     => [],
                    'payment_system' => 'paypal',
                    'gate_method_id'  => null,
                    'country_filter' => 'every',
                    'show_agreement_flag' => true
                ]
            ],
            [
                'id' => [ 'id' => 13 ],
                'data' => [
                    'order'         => 13,
                    'icon'          => 'cardRF.svg',
                    'titles'        => [
                        'ru' => 'Sepa',
                        'en' => 'Sepa',
                        'de' => 'Sepa',
                        'es' => 'Sepa',
                        'pt' => 'Sepa',
                        'it' => 'Sepa',
                        'tr' => 'Sepa',
                        'uk' => 'Sepa'
                    ],
                    'currencies'    => ['EUR'],
                    'limits'        => [
                        'EUR' => ['min' => 0.5, 'max' => 999999],
                    ],
                    'countries'     => [
                        'AT', 'AD', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE', 'GI', 'GR', 'HU', 'IS', 'IE',
                        'IT', 'LV', 'LI', 'LT', 'LU', 'MT', 'MC', 'NL', 'NO', 'PL', 'PT', 'RO', 'SM', 'SK', 'SI', 'ES', 'SE',
                        'СН', 'GB'
                    ],
                    'payment_system' => 'stripeEdTech',
                    'gate_method_id'  => 'sepa_debit',
                    'country_filter' => 'onlyAt',
                    'show_agreement_flag' => true
                ]
            ],
            [
                'id' => [ 'id' => 14 ],
                'data' => [
                    'order'         => 14,
                    'icon'          => 'bankTransfer.svg',
                    'titles'        => [
                        'ru' => 'Bank transfer',
                        'en' => 'Bank transfer',
                        'de' => 'Bank transfer',
                        'es' => 'Bank transfer',
                        'pt' => 'Bank transfer',
                        'it' => 'Bank transfer',
                        'tr' => 'Bank transfer',
                        'uk' => 'Bank transfer'
                    ],
                    'currencies'    => ['EUR'],
                    'limits'        => [
                        'EUR' => ['min' => 1, 'max' => 900],
                    ],
                    'countries'     => ['ES'],
                    'payment_system' => 'payop',
                    'gate_method_id'  => '200022',
                    'country_filter' => 'onlyAt',
                    'show_agreement_flag' => true
                ]
            ],
            [
                'id' => [ 'id' => 15 ],
                'data' => [
                    'order'         => 15,
                    'icon'          => 'bankTransfer.svg',
                    'titles'        => [
                        'ru' => 'Bank transfer',
                        'en' => 'Bank transfer',
                        'de' => 'Bank transfer',
                        'es' => 'Bank transfer',
                        'pt' => 'Bank transfer',
                        'it' => 'Bank transfer',
                        'tr' => 'Bank transfer',
                        'uk' => 'Bank transfer'
                    ],
                    'currencies'    => ['EUR'],
                    'limits'        => [
                        'EUR' => ['min' => 1, 'max' => 900],
                    ],
                    'countries'     => ['GB'],
                    'payment_system' => 'payop',
                    'gate_method_id'  => '203801',
                    'country_filter' => 'onlyAt',
                    'show_agreement_flag' => true
                ]
            ],
            [
                'id' => [ 'id' => 16 ],
                'data' => [
                    'order'         => 16,
                    'icon'          => 'bankTransfer.svg',
                    'titles'        => [
                        'ru' => 'Bank transfer',
                        'en' => 'Bank transfer',
                        'de' => 'Bank transfer',
                        'es' => 'Bank transfer',
                        'pt' => 'Bank transfer',
                        'it' => 'Bank transfer',
                        'tr' => 'Bank transfer',
                        'uk' => 'Bank transfer'
                    ],
                    'currencies'    => ['EUR'],
                    'limits'        => [
                        'EUR' => ['min' => 1, 'max' => 900],
                    ],
                    'countries'     => ['PT'],
                    'payment_system' => 'payop',
                    'gate_method_id'  => '200023',
                    'country_filter' => 'onlyAt',
                    'show_agreement_flag' => true
                ]
            ],
            [
                'id' => [ 'id' => 17 ],
                'data' => [
                    'order'         => 17,
                    'icon'          => 'revolut.svg',
                    'titles'        => [
                        'ru' => 'Revolut',
                        'en' => 'Revolut',
                        'de' => 'Revolut',
                        'es' => 'Revolut',
                        'pt' => 'Revolut',
                        'it' => 'Revolut',
                        'tr' => 'Revolut',
                        'uk' => 'Revolut'
                    ],
                    'currencies'    => ['EUR'],
                    'limits'        => [
                        'EUR' => ['min' => 1, 'max' => 900],
                    ],
                    'countries'     => ['GB'],
                    'payment_system' => 'payop',
                    'gate_method_id'  => '203821',
                    'country_filter' => 'onlyAt',
                    'show_agreement_flag' => true
                ]
            ],
            [
                'id' => [ 'id' => 18 ],
                'data' => [
                    'order'         => 18,
                    'icon'          => 'revolut.svg',
                    'titles'        => [
                        'ru' => 'Revolut',
                        'en' => 'Revolut',
                        'de' => 'Revolut',
                        'es' => 'Revolut',
                        'pt' => 'Revolut',
                        'it' => 'Revolut',
                        'tr' => 'Revolut',
                        'uk' => 'Revolut'
                    ],
                    'currencies'    => ['EUR'],
                    'limits'        => [
                        'EUR' => ['min' => 1, 'max' => 900],
                    ],
                    'countries'     => ['GB'],
                    'payment_system' => 'payop',
                    'gate_method_id'  => '3822',
                    'country_filter' => 'everyExcept',
                    'show_agreement_flag' => true
                ]
            ],
            [
                'id' => [ 'id' => 19 ],
                'data' => [
                    'order'         => 19,
                    'icon'          => 'poli.svg',
                    'titles'        => [
                        'ru' => 'Poli',
                        'en' => 'Poli',
                        'de' => 'Poli',
                        'es' => 'Poli',
                        'pt' => 'Poli',
                        'it' => 'Poli',
                        'tr' => 'Poli',
                        'uk' => 'Poli'
                    ],
                    'currencies'    => ['USD'],
                    'limits'        => [
                        'USD' => ['min' => 1, 'max' => 900],
                    ],
                    'countries'     => ['AU', 'NZ'],
                    'payment_system' => 'payop',
                    'gate_method_id'  => '3822',
                    'country_filter' => 'onlyAt',
                    'show_agreement_flag' => true
                ]
            ],
            [
                'id' => [ 'id' => 20 ],
                'data' => [
                    'order'         => 20,
                    'icon'          => 'advcash.svg',
                    'titles'        => [
                        'ru' => 'Advcash',
                        'en' => 'Advcash',
                        'de' => 'Advcash',
                        'es' => 'Advcash',
                        'pt' => 'Advcash',
                        'it' => 'Advcash',
                        'tr' => 'Advcash',
                        'uk' => 'Advcash'
                    ],
                    'currencies'    => ['USD', 'EUR'],
                    'limits'        => [
                        'USD' => ['min' => 1, 'max' => 900],
                        'EUR' => ['min' => 1, 'max' => 900],
                    ],
                    'countries'     => ['ES', 'GB', 'CA', 'AU', 'NZ', 'IE', 'PT'],
                    'payment_system' => 'payop',
                    //Advanced Cash
                    'gate_method_id'  => '765',
                    'country_filter' => 'onlyAt',
                    'show_agreement_flag' => true
                ]
            ],
            [
                'id' => [ 'id' => 21 ],
                'data' => [
                    'order'         => 21,
                    'icon'          => 'eWallet.svg',
                    'titles'        => [
                        'ru' => 'Online Wallet',
                        'en' => 'Online Wallet',
                        'de' => 'Online Wallet',
                        'es' => 'Online Wallet',
                        'pt' => 'Online Wallet',
                        'it' => 'Online Wallet',
                        'tr' => 'Online Wallet',
                        'uk' => 'Online Wallet'
                    ],
                    'currencies'    => ['USD'],
                    'limits'        => [
                        'USD' => ['min' => 1, 'max' => 900],
                    ],
                    'countries'     => ['US'],
                    'payment_system' => 'payop',
                    //Qiwi
                    'gate_method_id'  => '5101',
                    'country_filter' => 'onlyAt',
                    'show_agreement_flag' => true
                ]
            ]
        ];

        PaymentMethod::truncate();

        foreach ($items as $item) {
            PaymentMethod::firstOrCreate($item['id'], $item['data']);
        }
    }
}
