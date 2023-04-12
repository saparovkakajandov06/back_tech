<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\TransactionGroups;


class TransactionGroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TransactionGroups::create([
            'transaction_group' => 'GROUP_EARNED',
            'title' => 'Заработано',
        ]);

        TransactionGroups::create([
          'transaction_group' => 'GROUP_WITHDRAWN',
          'title' => 'Выведено',
        ]);

        TransactionGroups::create([
            'transaction_group' => 'GROUP_DEPOSITED',
            'title' => 'Сумма пополнений',
        ]);

        TransactionGroups::create([
            'transaction_group' => 'GROUP_UNKNOWN',
            'title' => 'Неизвестная группа',
        ]);
    }
}
