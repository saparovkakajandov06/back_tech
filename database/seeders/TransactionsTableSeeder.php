<?php

namespace Database\Seeders;

use App\Transaction;
use App\User;
use Illuminate\Database\Seeder;

class TransactionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Transaction::truncate();

//        factory(Transaction::class, 12)->create();

        User::where('name','admin')
            ->first()
            ->giveMoney(100, 'RUB')
            ->giveMoney(500, 'RUB');

            Transaction::create([
                'event_id' => '10',
                'user_id' => '104',
                'type' => 'INFLOW_CREATE',
                'amount' => '100',
                'comment' => 'Money created',
                'created_at' => '2019-01-01 10:45:07',
            ]);

            Transaction::create([
                'event_id' => '11',
                'user_id' => '104',
                'type' => 'INFLOW_CREATE',
                'amount' => '500',
                'comment' => 'Money created',
                'created_at' => '2020-02-01 10:45:07',
            ]);

            Transaction::create([
                'event_id' => '12',
                'user_id' => '104',
                'type' => 'INFLOW_PAYMENT',
                'comment' => 'INFLOW_PAYMENT',
                'amount' => '500',
                'created_at' => '2020-03-01 10:45:07',
            ]);

            Transaction::create([
                'event_id' => '13',
                'user_id' => '104',
                'type' => 'OUTFLOW_ORDER',
                'amount' => '-1',
                'comment' => 'Оплата заказа 1',
            ]);

            Transaction::create([
                'event_id' => '14',
                'user_id' => '104',
                'type' => 'INFLOW_PAYMENT',
                'amount' => '500',
                'comment' => 'Пополнение черея Яндекс 1',
            ]);

            Transaction::create([
                'event_id' => '15',
                'user_id' => '104',
                'type' => 'OUTFLOW_ORDER',
                'amount' => '-43.65',
                'comment' => 'Оплата заказа 3',
            ]);

            Transaction::create([
                'event_id' => '16',
                'user_id' => '104',
                'type' => 'INFLOW_REF_BONUS',
                'amount' => '4.3650',
                'related_user_id' => '103',
                'comment' => 'бонус',
            ]);

            Transaction::create([
                'event_id' => '17',
                'user_id' => '104',
                'type' => 'INFLOW_PAYMENT',
                'amount' => '19',
                'comment' => 'Средство для оплаты заказа 3',
            ]);

            Transaction::create([
                'event_id' => '18',
                'user_id' => '104',
                'type' => 'OUTFLOW_ORDER',
                'amount' => '-19',
                'comment' => 'Оплата заказа 2',
            ]);

            Transaction::create([
                'event_id' => '19',
                'user_id' => '104',
                'type' => 'OUTFLOW_WITHDRAWAL',
                'amount' => '-100',
                'comment' => '',
            ]);


            Transaction::create([
                'event_id' => '10',
                'user_id' => '104',
                'type' => 'INFLOW_CREATE',
                'amount' => '100',
                'comment' => 'Money created',
            ]);

            Transaction::create([
                'event_id' => '21',
                'user_id' => '104',
                'type' => 'INFLOW_CREATE',
                'amount' => '5000',
                'comment' => 'Money created',
            ]);

            Transaction::create([
                'event_id' => '22',
                'user_id' => '104',
                'type' => 'INFLOW_PAYMENT',
                'amount' => '5000',
                'comment' => 'Пополнение черея Яндекс 1',
            ]);

            Transaction::create([
                'event_id' => '13',
                'user_id' => '104',
                'type' => 'OUTFLOW_ORDER',
                'amount' => '-10',
                'comment' => 'Оплата заказа 1',
            ]);

            Transaction::create([
                'event_id' => '24',
                'user_id' => '104',
                'type' => 'INFLOW_PAYMENT',
                'amount' => '5000',
                'comment' => 'Пополнение черея Яндекс 1',
            ]);

            Transaction::create([
                'event_id' => '25',
                'user_id' => '104',
                'type' => 'OUTFLOW_ORDER',
                'amount' => '-403.65',
                'comment' => 'Оплата заказа 3',
            ]);

            Transaction::create([
                'event_id' => '26',
                'user_id' => '104',
                'type' => 'INFLOW_REF_BONUS',
                'amount' => '4.3650',
                'related_user_id' => '1003',
                'comment' => 'бонус',
            ]);

            Transaction::create([
                'event_id' => '27',
                'user_id' => '104',
                'type' => 'INFLOW_PAYMENT',
                'amount' => '109',
                'comment' => 'Средство для оплаты заказа 3',
            ]);

            Transaction::create([
                'event_id' => '28',
                'user_id' => '104',
                'type' => 'OUTFLOW_ORDER',
                'amount' => '-109',
                'comment' => 'Оплата заказа 2',
            ]);

            Transaction::create([
                'event_id' => '29',
                'user_id' => '104',
                'type' => 'OUTFLOW_WITHDRAWAL',
                'amount' => '-1000',
                'comment' => '',
            ]);
    }
}
