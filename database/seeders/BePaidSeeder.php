<?php

namespace Database\Seeders;

use App\PaymentMethod;
use App\Transaction;
use Illuminate\Database\Seeder;

class BePaidSeeder extends Seeder
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
            'order' => 4,
            'icon' => 'cardRF.svg',
            'titles' => [
                'ru' => 'Банковская карта (bePaid)',
                'en' => 'Credit card (bePaid)',
                'de' => 'Kreditkarte (bePaid)',
                'es' => 'Tarjeta de crédito (Stripe)',
                'pt' => 'Cartão de crédito (bePaid)',
                'it' => 'Carta di credito (bePaid)',
                'tr' => 'Kredi kartı (bePaid)',
                'uk' => 'Credit card (bePaid)'
            ],
            'currencies' => [
                Transaction::CUR_EUR,
                Transaction::CUR_RUB,
                Transaction::CUR_USD,
                Transaction::CUR_UZS
            ],
            'limits' => [
                Transaction::CUR_EUR => ['min' => 0.02, 'max' => 3325.19],
                Transaction::CUR_RUB => ['min' => 1, 'max' => 255000],
                Transaction::CUR_USD => ['min' => 0.02, 'max' => 3565.94],
                Transaction::CUR_UZS => ['min' => 158.65, 'max' => 40455535.98],
            ],
            'countries' => [
                'AZ', 'AM', 'BY', 'KZ', 'KG', 'MD', 'TJ', 'TM', 'UZ', 'UA'
            ],
            'payment_system' => 'bepaid',
            'gate_method_id' => null,
            'country_filter' => 'onlyAt',
            'show_agreement_flag' => true
        ];

        PaymentMethod::create($item);
    }
}
