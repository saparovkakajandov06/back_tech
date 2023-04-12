<?php

namespace Database\Seeders;

use App\Domain\Models\CompositeOrder;
use App\Order;
use App\User;
use App\UserService;
use Illuminate\Database\Seeder;

class PayloadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $servicesCount = 100;
        $usersCount = 100;
        $ordersCount = 1000;

        $userServices = factory(UserService::class, $servicesCount)->create();
//        $lastServiceId = UserService::latest()->first()->id;

        $users = factory(User::class, $usersCount)->create();
//        $lastUserId = User::latest()->first()->id;

        $statuses = collect([
            Order::STATUS_RUNNING,
            Order::STATUS_COMPLETED,
            Order::STATUS_ERROR
        ]);

        factory(CompositeOrder::class, $ordersCount)->create([
            'user_id' => fn() => rand(1004, 1004 + $usersCount - 1),
            'user_service_id' => fn() => rand(28, 28 + $servicesCount - 1),
            'status' => fn() => $statuses->random(),
        ]);
    }
}
