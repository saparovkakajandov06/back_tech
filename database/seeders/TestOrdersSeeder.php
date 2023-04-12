<?php

namespace Database\Seeders;

use App\Domain\Models\CompositeOrder;
use App\Order;
use App\Transaction;
use App\User;
use App\UserService;
use Illuminate\Database\Seeder;

class TestOrdersSeeder extends Seeder
{
    const USER_1_ID = 452435;
    const USER_1_NAME = 'some_random_xxx';
    const USER_1_EMAIL = 'email_xxx@smmtouch.one';

    const USER_2_ID = 5186;
    const USER_2_NAME = 'some_random_yyy';
    const USER_2_EMAIL = 'email_yyy@smmtouch.two';

    const USER_3_ID = 3214;
    const USER_3_NAME = 'some_random_qqq';
    const USER_3_EMAIL = 'email_qqq@smmtouch.three';

    const USER_4_ID = 159877;
    const USER_4_NAME = 'some_random_zzz';
    const USER_4_EMAIL = 'email_zzz@smmtouch.san';

    const LINK_1 = 'https://www.instagram.com/p/first_code';
    const LINK_2 = 'https://www.instagram.com/p/second_code';
    const LINK_3 = 'https://www.instagram.com/p/third_code';

    const LOGIN_1 = 'ig_login_1';
    const LOGIN_2 = 'ig_login_2';
    const LOGIN_3 = 'ig_login_3';

    const LINK_WITH_LOGIN_3 = 'https://www.instagram.com/p/' . self::LOGIN_3;

    const TAG_1 = 'SOME_RANDOM_TAG_1';
    const TAG_2 = 'SOME_RANDOM_TAG_2';
    const TAG_3 = 'SOME_RANDOM_TAG_3';

    const US_1_ID = 5134;
    const US_2_ID = 163;
    const US_3_ID = 69256;

    // search works only on paid orders
    private const data = [
        [
            'id' => 1,
            'user_service_id' => self::US_1_ID,
            'user_id' => self::USER_1_ID,
            'status' => Order::STATUS_RUNNING,
            'created_at' => '2021-08-01',
            'params' => [
                'cur' => Transaction::CUR_RUB,
                'cost' => 100.00,
                'link' => self::LINK_1,
            ]
        ],
        [
            'id' => 2,
            'user_service_id' => self::US_1_ID,
            'user_id' => self::USER_2_ID,
            'status' => Order::STATUS_COMPLETED,
            'created_at' => '2021-08-02',
            'params' => [
                'cur' => Transaction::CUR_RUB,
                'cost' => 99.99,
                'link' => self::LINK_2,
            ]
        ],
        [
            'id' => 3,
            'user_service_id' => self::US_2_ID,
            'user_id' => self::USER_1_ID,
            'status' => Order::STATUS_PARTIAL_COMPLETED,
            'created_at' => '2021-08-03',
            'params' => [
                'cur' => Transaction::CUR_RUB,
                'cost' => 100.01,
                'link' => self::LINK_3,
            ]
        ],
        [
            'id' => 4,
            'user_service_id' => self::US_2_ID,
            'user_id' => self::USER_2_ID,
            'status' => Order::STATUS_ERROR,
            'created_at' => '2021-08-04',
            'params' => [
                'cur' => Transaction::CUR_USD,
                'cost' => 100.00,
                'login' => self::LOGIN_1,
            ]
        ],
        [
            'id' => 5,
            'user_service_id' => self::US_2_ID,
            'user_id' => self::USER_1_ID,
            'status' => Order::STATUS_PAUSED,
            'created_at' => '2021-08-05',
            'params' => [
                'cur' => Transaction::CUR_USD,
                'cost' => 100.00,
                'login' => self::LOGIN_2,
            ]
        ],
        [
            'id' => 6,
            'user_service_id' => self::US_3_ID,
            'user_id' => self::USER_2_ID,
            'status' => Order::STATUS_UPDATING,
            'created_at' => '2021-08-06',
            'params' => [
                'cur' => Transaction::CUR_USD,
                'cost' => 100.00,
                'login' => self::LOGIN_3,
            ]
        ],
        [
            'id' => 7,
            'user_service_id' => self::US_3_ID,
            'user_id' => self::USER_1_ID,
            'status' => Order::STATUS_CANCELED,
            'created_at' => '2021-08-07',
            'params' => [
                'cur' => Transaction::CUR_USD,
                'cost' => 100.00,
                'link' => self::LINK_WITH_LOGIN_3,
            ]
        ],
        [
            'id' => 8,
            'user_service_id' => self::US_3_ID,
            'user_id' => self::USER_4_ID,
            'status' => Order::STATUS_RUNNING,
            'created_at' => '2021-08-15 21:00:00', // 2021-08-15T18:00:00.000Z
            'params' => [
                'cur' => Transaction::CUR_RUB,
                'cost' => 1000.00,
                'link' => self::LINK_2,
            ]
        ],
        [
            'id' => 9,
            'user_service_id' => self::US_3_ID,
            'user_id' => self::USER_4_ID,
            'status' => Order::STATUS_CANCELED,
            'created_at' => '2021-08-16 03:00:00', // 2021-08-16T00:00:00.000Z
            'params' => [
                'cur' => Transaction::CUR_RUB,
                'cost' => 1000.00,
                'link' => self::LINK_2,
            ]
        ],
        [
            'id' => 10,
            'user_service_id' => self::US_3_ID,
            'user_id' => self::USER_4_ID,
            'status' => Order::STATUS_COMPLETED,
            'created_at' => '2021-08-16 21:00:00', // 2021-08-16T18:00:00.000Z
            'params' => [
                'cur' => Transaction::CUR_RUB,
                'cost' => 1000.00,
                'link' => self::LINK_2,
            ]
        ],
        [
            'id' => 11,
            'user_service_id' => self::US_3_ID,
            'user_id' => self::USER_4_ID,
            'status' => Order::STATUS_ERROR,
            'created_at' => '2021-08-17 03:00:00', // 2021-08-17T00:00:00.000Z
            'params' => [
                'cur' => Transaction::CUR_RUB,
                'cost' => 1000.00,
                'link' => self::LINK_2,
            ]
        ],
    ];

    public function run()
    {
        \DB::table('composite_orders')->delete();

        User::factory()->create([
            'id' => self::USER_1_ID,
            'name' => self::USER_1_NAME,
            'email' => self::USER_1_EMAIL,
            'cur' => 'USD',
        ]);

        User::factory()->create([
            'id' => self::USER_2_ID,
            'name' => self::USER_2_NAME,
            'email' => self::USER_2_EMAIL,
            'cur' => 'RUB',
        ]);

        User::factory()->create([
            'id' => self::USER_3_ID,
            'name' => self::USER_3_NAME,
            'email' => self::USER_3_EMAIL,
            'cur' => 'RUB',
        ]);

        User::factory()->create([
            'id' => self::USER_4_ID,
            'name' => self::USER_4_NAME,
            'email' => self::USER_4_EMAIL,
            'cur' => 'RUB',
        ]);

        UserService::factory()->create([
            'id' => self::US_1_ID,
            'tag' => self::TAG_1,
            'platform' => 'Tiktok'
        ]);

        UserService::factory()->create([
            'id' => self::US_2_ID,
            'tag' => self::TAG_2,
        ]);

        UserService::factory()->create([
            'id' => self::US_3_ID,
            'tag' => self::TAG_3,
        ]);

        foreach (self::data as $d) {
            CompositeOrder::factory()->create($d);
        }
    }
}
