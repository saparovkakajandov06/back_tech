<?php

namespace Database\Seeders;

use App\TransactionTypes;
use Illuminate\Database\Seeder;

class TransactionTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TransactionTypes::create([
            'transaction_type' => 'GROUP_EARNED',
            'title' => 'Заработано',
            'transaction_group_id' => 4,
        ]);

        TransactionTypes::create([
            'transaction_type' => 'INFLOW_REF_BONUS',
            'title' => 'Бонус',
            'transaction_group_id' => 1,
        ]);

        TransactionTypes::create([
            'transaction_type' => 'INFLOW_USER_JOB',
            'title' => 'Задание',
            'transaction_group_id' => 4,
        ]);

        TransactionTypes::create([
            'transaction_type' => 'GROUP_WITHDRAWN',
            'title' => '',
            'transaction_group_id' => 4,
        ]);

        TransactionTypes::create([
            'transaction_type' => 'OUTFLOW_WITHDRAWAL',
            'title' => '',
            'transaction_group_id' => 2,
        ]);

        TransactionTypes::create([
            'transaction_type' => 'GROUP_DEPOSITED',
            'title' => '',
            'transaction_group_id' => 4,
        ]);

        TransactionTypes::create([
            'transaction_type' => 'INFLOW_TEST',
            'title' => '',
            'transaction_group_id' => 3,
        ]);

        TransactionTypes::create([
            'transaction_type' => 'INFLOW_OTHER',
            'title' => '',
            'transaction_group_id' => 3,
        ]);

        TransactionTypes::create([
            'transaction_type' => 'INFLOW_CREATE',
            'title' => 'Деньги из воздуха',
            'transaction_group_id' => 3,
        ]);

        TransactionTypes::create([
            'transaction_type' => 'INFLOW_REFUND',
            'title' => 'Возврат',
            'transaction_group_id' => 3,
        ]);

        TransactionTypes::create([
            'transaction_type' => 'INFLOW_PAYMENT',
            'title' => 'Через платежную систему',
            'transaction_group_id' => 3,
        ]);

        TransactionTypes::create([
            'transaction_type' => 'GROUP_UNKNOWN',
            'title' => '',
            'transaction_group_id' => 4,
        ]);

        TransactionTypes::create([
            'transaction_type' => 'UNKNOWN',
            'title' => '',
            'transaction_group_id' => 4,
        ]);

        TransactionTypes::create([
            'transaction_type' => 'OUTFLOW_TEST',
            'title' => '',
            'transaction_group_id' => 4,
        ]);

        TransactionTypes::create([
            'transaction_type' => 'OUTFLOW_OTHER',
            'title' => '',
            'transaction_group_id' => 4,
        ]);

        TransactionTypes::create([
            'transaction_type' => 'OUTFLOW_ORDER',
            'title' => '',
            'transaction_group_id' => 4,
        ]);

    }
}
