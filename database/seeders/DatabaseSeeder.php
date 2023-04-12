<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run() {
        $this->call([
            UsersTableSeeder::class,
            TransactionsTableSeeder::class,
            TransactionGroupsTableSeeder::class,
            TransactionTypesTableSeeder::class,
            OrdersTableSeeder::class,
            PremiumStatusesTableSeeder::class,
            TestOrdersSeeder::class,
            NotificationSeeder::class,
            PriceFeatureSeeder::class,
            PriceCategorySeeder::class,
            //PriceSeeder::class,

            TestUserServicesTableSeeder::class,
        ]);
//        $this->call(USPricesTableGeneratedSeeder::class);
        $this->call(UserServicesTableNewSeeder::class);
        $this->call(PremiumStatusesLabelsSeeder::class);
    }
}