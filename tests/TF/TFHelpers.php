<?php

namespace Tests\TF;

use Database\Seeders\PremiumStatusesLabelsSeeder;
use Database\Seeders\TestUserServicesTableSeeder;
use Database\Seeders\UserServicesTableNewSeeder;
use Database\Seeders\UsersTableSeeder;

class TFHelpers
{
    public static function runCommonSeeders()
    {
        $seeders = [
            UsersTableSeeder::class,
            PremiumStatusesLabelsSeeder::class,
            UserServicesTableNewSeeder::class,
//            USPricesTableGeneratedSeeder::class,
//            TestUserServicesTableSeeder::class,
        ];

        foreach ($seeders as $seeder) {
            (new $seeder)->run();
        }
    }

    public static function runTestSeeders()
    {
        $seeders = [
            UsersTableSeeder::class,
            PremiumStatusesLabelsSeeder::class,
//            UserServicesTableGeneratedSeeder::class,
//            USPricesTableGeneratedSeeder::class,
            TestUserServicesTableSeeder::class,
        ];

        foreach ($seeders as $seeder) {
            (new $seeder)->run();
        }
    }
}
