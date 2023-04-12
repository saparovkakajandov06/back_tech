<?php

namespace Database\Seeders;

use App\Transaction;
use App\User;
use Illuminate\Database\Seeder;

class TestTransactionsSeeder extends Seeder
{
    private function getData($user)
    {
        return [
            [
                'id' => 1,
                'user_id' => $user->id + random_int(10, 1000),
                'amount' => 10.00,
                'comment' => null,
                'created_at' => '2020-08-01',
                'cur' => Transaction::CUR_RUB,
                'type' => Transaction::INFLOW_CREATE,
            ],
            [
                'id' => 2,
                'user_id' => $user->id,
                'amount' => 11.00,
                'comment' => null,
                'created_at' => '2020-08-02',
                'cur' => Transaction::CUR_RUB,
                'type' => Transaction::INFLOW_PAYMENT,
            ],
            [
                'id' => 3,
                'user_id' => $user->id,
                'amount' => 12.00,
                'comment' => null,
                'created_at' => '2020-08-03',
                'cur' => Transaction::CUR_RUB,
                'type' => Transaction::INFLOW_REF_BONUS,
            ],
            [
                'id' => 4,
                'user_id' => $user->id,
                'amount' => 13.00,
                'comment' => null,
                'created_at' => '2020-08-04',
                'cur' => Transaction::CUR_RUB,
                'type' => Transaction::INFLOW_REFUND,
            ],
            [
                'id' => 5,
                'user_id' => $user->id,
                'amount' => 14.00,
                'comment' => null,
                'created_at' => '2020-08-05',
                'cur' => Transaction::CUR_RUB,
                'type' => Transaction::INFLOW_PAYMENT,
            ],
            [
                'id' => 6,
                'user_id' => $user->id,
                'amount' => 15.00,
                'comment' => null,
                'created_at' => '2020-08-06',
                'cur' => Transaction::CUR_RUB,
                'type' => Transaction::INFLOW_REF_BONUS,
            ],
            [
                'id' => 7,
                'user_id' => $user->id,
                'amount' => 16.00,
                'comment' => null,
                'created_at' => '2020-08-07',
                'cur' => Transaction::CUR_RUB,
                'type' => Transaction::INFLOW_CREATE,
            ],
            [
                'id' => 8,
                'user_id' => $user->id,
                'amount' => 17.00,
                'comment' => null,
                'created_at' => '2020-08-08',
                'cur' => Transaction::CUR_RUB,
                'type' => Transaction::INFLOW_CREATE,
            ],
            [
                'id' => 9,
                'user_id' => $user->id,
                'amount' => 18.00,
                'comment' => null,
                'created_at' => '2020-08-09',
                'cur' => Transaction::CUR_RUB,
                'type' => Transaction::INFLOW_OTHER,
            ],
            [
                'id' => 10,
                'user_id' => $user->id,
                'amount' => 19.00,
                'comment' => null,
                'created_at' => '2020-08-10',
                'cur' => Transaction::CUR_USD,
                'type' => Transaction::INFLOW_CREATE,
            ],
        ];
    }

    public function run(User $user)
    {
        \DB::table('transactions')->delete();

        foreach ($this->getData($user) as $d) {
            Transaction::factory()->create($d);
        }
    }
}
