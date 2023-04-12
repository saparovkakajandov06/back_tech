<?php

namespace Database\Seeders;

use App\PaymentMethod;
use App\Transaction;
use Illuminate\Database\Seeder;

class PayToDaySeeder extends Seeder
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
                'ru' => 'Международная карта',
                'en' => 'Credit card (Stripe)',
                'de' => 'Kreditkarte (Stripe)',
                'es' => 'Tarjeta de crédito (Stripe)',
                'pt' => 'Cartão de crédito (Stripe)',
                'it' => 'Carta di credito (Stripe)',
                'tr' => 'Kredi kartı (Stripe)',
                'uk' => 'Credit card (Stripe)'
            ],
            'currencies' => [Transaction::CUR_RUB],
            'limits' => [
                Transaction::CUR_RUB => ['min' => 40, 'max' => 100000],
            ],
            'countries' => ['US', 'ME', 'CA', 'LV'],
            'payment_system' => 'paytoday',
            'gate_method_id' => null,
            'country_filter' => 'onlyAt',
            'show_agreement_flag' => true
        ];

        PaymentMethod::create($item);

        $item = [
            //Fix pg autoicncrement
            'id' => PaymentMethod::max('id') + 1,
            'order' => 10,
            'icon' => 'cardRF.svg',
            'titles' => [
                'ru' => 'Банковская карта',
                'en' => 'Credit card',
                'de' => 'Kreditkarte',
                'es' => 'Tarjeta de crédito',
                'pt' => 'Cartão de crédito',
                'it' => 'Carta di credito',
                'tr' => 'Kredi kartı',
                'uk' => 'Credit card'
            ],
            'currencies' => [Transaction::CUR_EUR, Transaction::CUR_USD],
            'limits' => [
                Transaction::CUR_EUR => ['min' => 0.5, 'max' => 100000],
                Transaction::CUR_USD => ['min' => 0.5, 'max' => 100000],
            ],
            'countries' => [],
            'payment_system' => 'paytoday',
            'gate_method_id' => null,
            'country_filter' => 'every',
            'show_agreement_flag' => true
        ];

        PaymentMethod::create($item);
    }
}
