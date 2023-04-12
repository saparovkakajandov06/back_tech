<?php

namespace Database\Seeders;

use App\PaymentMethod;
use App\Transaction;
use Illuminate\Database\Seeder;

class MonerchySeeder extends Seeder
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
            'order' => 14,
            'icon' => 'cardRF.svg',
            'titles' => [
                'ru' => 'Sepa',
                'en' => 'Sepa',
                'de' => 'Sepa',
                'es' => 'Sepa',
                'pt' => 'Sepa',
                'it' => 'Sepa',
                'tr' => 'Sepa',
                'uk' => 'Sepa'
            ],
            'currencies' => [Transaction::CUR_EUR],
            'limits' => [
                Transaction::CUR_EUR => ['min' => 2, 'max' => 10000],
            ],
            'countries' => [
                'AT', 'AD', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE', 'GI', 'GR', 'HU', 'IS', 'IE',
                'IT', 'LV', 'LI', 'LT', 'LU', 'MT', 'MC', 'NL', 'NO', 'PL', 'PT', 'RO', 'SM', 'SK', 'SI', 'ES', 'SE',
                'СН', 'GB', 'GF', 'GP', 'GG', 'IM', 'JE', 'MQ', 'YT', 'RE', 'BL', 'MF', 'PM'
            ],
            'payment_system' => 'monerchy',
            'gate_method_id' => null,
            'country_filter' => 'onlyAt',
            'show_agreement_flag' => true
        ];

        PaymentMethod::create($item);
    }
}
