<?php

namespace Database\Seeders;

use App\PaymentMethod;
use App\Transaction;
use Illuminate\Database\Seeder;

class PayzeSeeder extends Seeder
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
            'order' => 1,
            'icon' => 'cardRF.svg',
            'titles' => [
                'ru' => 'Банковская карта (Payze)',
                'en' => 'Credit card (Payze)',
                'de' => 'Credit card (Payze)',
                'es' => 'Credit card (Payze)',
                'pt' => 'Credit card (Payze)',
                'it' => 'Credit card (Payze)',
                'tr' => 'Credit card (Payze)',
                'uk' => 'Credit card (Payze)'
            ],
            'currencies' => [Transaction::CUR_UZS, Transaction::CUR_USD],
            'limits' => [
                Transaction::CUR_UZS => ['min' => 1100, 'max' => 6850000],
                Transaction::CUR_USD => ['min' => 1, 'max' => 650],
            ],
            'countries' => ['UZ'],
            'payment_system' => 'payze',
            'gate_method_id' => null,
            'country_filter' => 'onlyAt',
            'show_agreement_flag' => true
        ];

        PaymentMethod::create($item);

        $payMore = PaymentMethod::where('id', '=', 9)->firstOrFail();

        $payMore->countries = ['UZ'];
        $payMore->country_filter = 'everyExcept';

        $payMore->saveOrFail();
    }
}
