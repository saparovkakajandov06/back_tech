<?php

namespace Database\Seeders;

use App\PaymentMethod;
use Illuminate\Database\Seeder;

class UZSCryptocloudSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cryptoCloud = PaymentMethod::where('id', '=', 7)->firstOrFail();

        $cryptoCloud->currencies = ['RUB', 'EUR', 'USD', 'UZS'];

        $limits = $cryptoCloud->limits;

        $limits['UZS'] = [
            'min' => 5655.53,
            'max' => 99999999
        ];

        $cryptoCloud->limits = $limits;

        $cryptoCloud->saveOrFail();
    }
}
