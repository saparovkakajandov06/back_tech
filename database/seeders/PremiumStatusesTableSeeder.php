<?php

namespace Database\Seeders;

use App\Constants;
use App\PremiumStatus;
use App\UserService;
use Illuminate\Database\Seeder;

class PremiumStatusesTableSeeder extends Seeder {

    public function run() {
        PremiumStatus::create([
            'name' => 'LEVEL_1',

            'online_support' => 1,
            'personal_manager' => 0,

            'discount' => [
                UserService::GROUP_LIKES => 0,
                UserService::GROUP_VIEWS => 0,
                UserService::GROUP_SUBS => 0,
                UserService::GROUP_COMMENTS => 0,
                UserService::GROUP_OTHER => 0,
            ],

            'cash' => 0,
        ]);

        PremiumStatus::create([
            'name' => 'LEVEL_2',

            'online_support' => 1,
            'personal_manager' => 0,

            'discount' => [
                UserService::GROUP_LIKES => 5,
                UserService::GROUP_VIEWS => 7,
                UserService::GROUP_SUBS => 3
            ],

            'cash' => 5000,
        ]);

        PremiumStatus::create([
            'name' => 'LEVEL_3',

            'online_support' => 1,
            'personal_manager' => 0,

            'discount' => [
                UserService::GROUP_LIKES => 10,
                UserService::GROUP_VIEWS => 15,
                UserService::GROUP_SUBS => 7,
                UserService::GROUP_COMMENTS => 10,
                UserService::GROUP_OTHER => 7,
            ],

            'cash' => 25000,
        ]);

        PremiumStatus::create([
            'name' => 'LEVEL_4',

            'online_support' => 1,
            'personal_manager' => 0,

            'discount' => [
                UserService::GROUP_LIKES => 15,
                UserService::GROUP_VIEWS => 25,
                UserService::GROUP_SUBS => 10,
                UserService::GROUP_COMMENTS => 15,
                UserService::GROUP_OTHER => 10,
            ],

            'cash' => 50000,
        ]);

        PremiumStatus::create([
            'name' => 'LEVEL_5',

            'online_support' => 1,
            'personal_manager' => 1,

            'discount' => [
                UserService::GROUP_LIKES => 20,
                UserService::GROUP_VIEWS => 30,
                UserService::GROUP_SUBS => 15,
                UserService::GROUP_COMMENTS => 20,
                UserService::GROUP_OTHER => 15,
            ],

            'cash' => 100000,
        ]);
    }
}
