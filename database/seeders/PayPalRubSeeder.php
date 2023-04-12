<?php

namespace Database\Seeders;

use App\PaymentMethod;
use App\Transaction;
use Illuminate\Database\Seeder;

class PayPalRubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $item = [
            //Fix pg autoicncrement
            'id' => PaymentMethod::max('id') + 1,
            'order' => 101,
            'icon' => 'payPal.svg',
            'titles' => [
                'ru' => 'PayPal',
                'en' => 'PayPal',
                'de' => 'PayPal',
                'es' => 'PayPal',
                'pt' => 'PayPal',
                'it' => 'PayPal',
                'tr' => 'PayPal',
                'uk' => 'PayPal'
            ],
            'currencies' => [Transaction::CUR_RUB],
            'limits' => [
                Transaction::CUR_RUB => ['min' => 24, 'max' => 2230825],
            ],
            'countries' => [
                'AT', 'PT', 'BE', 'BG', 'ES', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 'GF', 'DE', 'GI', 'GR', 'GP', 'GG', 'HU', 'IS', 'IE', 'IM',
                'IT', 'JE', 'LV', 'LI', 'LT', 'LU', 'MT', 'MQ', 'YT', 'MC', 'NL', 'NO', 'PL', 'RE', 'RO', 'BL', 'MF', 'PM', 'SM', 'SK', 'SI', 'SE',
                'CH', 'GB', 'US', 'MD', 'AL', 'ME', 'MK', 'RS', 'BA', 'XK'
            ],
            'payment_system' => 'paypalRubToEur',
            'gate_method_id' => null,
            'country_filter' => 'onlyAt',
            'show_agreement_flag' => true
        ];

        PaymentMethod::create($item);

        $item['id'] = PaymentMethod::max('id') + 1;
        $item['payment_system'] = 'paypalRubToUsd';
        $item['country_filter'] = 'everyExcept';

        PaymentMethod::create($item);
    }
}
