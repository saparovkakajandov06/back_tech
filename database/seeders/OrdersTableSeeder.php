<?php

namespace Database\Seeders;

use App\Constants;
use App\Order;
use App\Service;
use Illuminate\Database\Seeder;

class OrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Order::truncate();

//        factory(Order::class, 8)->create([ // fakes
//           'user_id' => 102,
//        ]);

//        $vkFake = Service::getByType(Constants::SERVICE_VK_FAKE);
//        factory(Order::class, 5)->make([
//            'user_id' => 102,
//            'service_id' => $vkFake->id,
//            'type' => $vkFake->type,
//        ]);
//
//        $autoFake = Service::getByType(Constants::SERVICE_AUTO_FAKE);
//        factory(Order::class, 5)->make([
//            'user_id' => 102,
//            'service_id' => $autoFake->id,
//            'type' => $autoFake->type,
//        ]);
    }
}
