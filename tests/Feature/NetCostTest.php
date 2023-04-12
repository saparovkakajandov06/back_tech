<?php

namespace Tests\Feature;

use App\Domain\Models\Chunk;
use App\Domain\Models\Slots;
use App\Domain\Services\AbstractService;
use App\Domain\Services\Vkserfing\AVkserfing;
use App\Domain\Services\Vkserfing\VkserfingFake;
use App\Role\UserRole;
use App\Transaction;
use App\User;
use App\UserService;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\TF\TFHelpers;

class NetCostTest extends TestCase
{
    use DatabaseMigrations;

    const link = 'http://example_link_to';

    public $admin;
    public $user;

    public function setUp(): void
    {
        parent::setUp();
        TFHelpers::runTestSeeders();

        $this->admin = User::factory()->create([
            'roles' => [UserRole::ROLE_ADMIN],
        ]);

        $this->user = User::factory()->create();
        $this->user->giveMoney(2000.0, Transaction::CUR_RUB);
    }

    public function createOrder(array $params)
    {
        $default = [
            'link' => 'http://link_to',
            'count' => '0',
            'tag' => 'SOME_BAD_TAG',
            'api_token' => null,
            'region_value' => 'CIS',
            'force_cur' => Transaction::CUR_RUB,
            'country_value' => 'RU',
        ];

        return $this->post(
            '/api/c_orders',
            array_merge($default, $params)
        );
    }

    // set config for instagram likes
    public function setConfig(array $config)
    {
        UserService::where('tag', UserService::INSTAGRAM_LIKES_LK)
            ->firstOrFail()
            ->update(['config' => $config]);
    }

    public function provider1()
    {
        return [
            [5, 100, 5],
            [15, 1000, 27],
            [rand(1, 1000), rand(1, 1000), rand(5, 500)],
            [rand(1, 1000), rand(1, 1000), rand(5, 500)],
            [rand(1, 1000) / rand(1, 1000), rand(1, 1000), rand(5, 500)],
            [rand(1, 1000) / rand(1, 1000), rand(1, 1000), rand(5, 500)],
        ];
    }

    /** @dataProvider provider1 */
    public function testLocalMode($netCost, $amount, $count)
    {
        // count without mods
        $this->setConfig([
            [
                'name' => Slots::INSTAGRAM_LIKES_LK_VKSERFING,
                'service_class' => AVkserfing::class,
                'order' => 1,
                'min' => 5,
                'max' => 500,
                'remote_params' => [
                    'type' => 'instagram_like',
                ],
                "net_cost" => [
                    "amount" => $amount,
                    "local" => $netCost,
                    'mode' => AbstractService::NET_COST_LOCAL,
                    'auto' => 123,
                    'auto_timestamp' => null,
                ],
            ]
        ]);

        $this->createOrder([
            'tag' => UserService::INSTAGRAM_LIKES_LK,
            'count' => $count,
            'api_token' => $this->user->api_token,
            'link' => self::link,
        ])
            ->assertJson(['status' => 'success']);

        $chunk = Chunk::findOrFail(1);
        $this->assertEquals([
            'slot' => Slots::INSTAGRAM_LIKES_LK_VKSERFING,
            'link' => self::link,
            'count' => $count,
            'charge' => $count * $netCost / $amount,
        ], $chunk->details);
    }

    /** @dataProvider provider1 */
    public function testRemoteMode($netCost, $amount, $count)
    {
        // count without mods
        $this->setConfig([
            [
                'name' => Slots::INSTAGRAM_LIKES_LK_VKSERFING,
                'service_class' => AVkserfing::class,
                'order' => 1,
                'min' => 5,
                'max' => 500,
                'remote_params' => [
                    'type' => 'instagram_like',
                ],
                "net_cost" => [
                    "amount" => $amount,
                    "local" => 0,
                    'mode' => AbstractService::NET_COST_REMOTE,
                    'auto' => 0,
                    'auto_timestamp' => null,
                ],
            ]
        ]);

        $this->createOrder([
            'tag' => UserService::INSTAGRAM_LIKES_LK,
            'count' => $count,
            'api_token' => $this->user->api_token,
            'link' => self::link,
        ])
            ->assertJson(['status' => 'success']);

        $chunk = Chunk::findOrFail(1);
        $this->assertEquals([
            'slot' => Slots::INSTAGRAM_LIKES_LK_VKSERFING,
            'link' => self::link,
            'count' => $count,
            'charge' => VkserfingFake::CHARGE,
        ], $chunk->details);
    }

    /** @dataProvider provider1 */
    public function testAutoModeReadValue($netCost, $amount, $count)
    {
        $now = Carbon::now()->toString();

        // count without mods
        $this->setConfig([
            [
                'name' => Slots::INSTAGRAM_LIKES_LK_VKSERFING,
                'service_class' => AVkserfing::class,
                'order' => 1,
                'min' => 5,
                'max' => 500,
                'remote_params' => [
                    'type' => 'instagram_like',
                ],
                "net_cost" => [
                    "amount" => $amount,
                    "local" => 0,
                    'mode' => AbstractService::NET_COST_AUTO,
                    'auto' => $netCost,
                    'auto_timestamp' => $now,
                ],
            ]
        ]);

        $this->createOrder([
            'tag' => UserService::INSTAGRAM_LIKES_LK,
            'count' => $count,
            'api_token' => $this->user->api_token,
            'link' => self::link,
        ])
            ->assertJson(['status' => 'success']);

        $chunk = Chunk::findOrFail(1);
        $this->assertEquals([
            'slot' => Slots::INSTAGRAM_LIKES_LK_VKSERFING,
            'link' => self::link,
            'count' => $count,
            'charge' => $count * $netCost / $amount,
        ], $chunk->details);

        $slot = Slots::getConfig(Slots::INSTAGRAM_LIKES_LK_VKSERFING);
        // data not changed
        $this->assertEquals(
            $now, data_get($slot, 'net_cost.auto_timestamp'));
        // value not changed
        $this->assertEquals(
            $netCost, data_get($slot, 'net_cost.auto'));
    }

    public function provider2()
    {
        return [
            [100, 5],
            [1000, 27],
            [rand(1, 1000), rand(5, 500)],
        ];
    }

    /** @dataProvider provider2 */
    public function testAutoModeShouldUpdateNullTimestamp($amount, $count)
    {
        // count without mods
        $this->setConfig([
            [
                'name' => Slots::INSTAGRAM_LIKES_LK_VKSERFING,
                'service_class' => AVkserfing::class,
                'order' => 1,
                'min' => 5,
                'max' => 500,
                'remote_params' => [
                    'type' => 'instagram_like',
                ],
                "net_cost" => [
                    "amount" => $amount,
                    "local" => 0,
                    'mode' => AbstractService::NET_COST_AUTO,
                    'auto' => 0,
                    'auto_timestamp' => null,
                ],
            ]
        ]);

        $this->createOrder([
            'tag' => UserService::INSTAGRAM_LIKES_LK,
            'count' => $count,
            'api_token' => $this->user->api_token,
            'link' => self::link,
        ])
            ->assertJson(['status' => 'success']);

        $chunk = Chunk::findOrFail(1);
        $this->assertEquals([
            'slot' => Slots::INSTAGRAM_LIKES_LK_VKSERFING,
            'link' => self::link,
            'count' => $count,
            'charge' => VkserfingFake::CHARGE, // full order price
        ], $chunk->details);

        $slot = Slots::getConfig(Slots::INSTAGRAM_LIKES_LK_VKSERFING);

        // delta should be < 5 seconds
        $ts = Carbon::parse(data_get($slot, 'net_cost.auto_timestamp'));
        $delta = Carbon::now()->diffInSeconds($ts); // returns positive int
        $this->assertLessThan(5, $delta);

        // value changed
        $this->assertEquals(
            (VkserfingFake::CHARGE / $count) * $amount, data_get($slot, 'net_cost.auto'));
    }

    /** @dataProvider provider2 */
    public function testAutoModeShouldUpdateOldTimestamp($amount, $count)
    {
        // count without mods
        $this->setConfig([
            [
                'name' => Slots::INSTAGRAM_LIKES_LK_VKSERFING,
                'service_class' => AVkserfing::class,
                'order' => 1,
                'min' => 5,
                'max' => 500,
                'remote_params' => [
                    'type' => 'instagram_like',
                ],
                "net_cost" => [
                    "amount" => $amount,
                    "local" => 0,
                    'mode' => AbstractService::NET_COST_AUTO,
                    'auto' => 0,
                    'auto_timestamp' => Carbon::now()->toString(),
                ],
            ]
        ]);

        $this->travel(AbstractService::TTL_HOURS + 1)->hours();

        $this->createOrder([
            'tag' => UserService::INSTAGRAM_LIKES_LK,
            'count' => $count,
            'api_token' => $this->user->api_token,
            'link' => self::link,
        ])
            ->assertJson(['status' => 'success']);

        $chunk = Chunk::findOrFail(1);
        $this->assertEquals([
            'slot' => Slots::INSTAGRAM_LIKES_LK_VKSERFING,
            'link' => self::link,
            'count' => $count,
            'charge' => VkserfingFake::CHARGE, // full order price
        ], $chunk->details);

        $slot = Slots::getConfig(Slots::INSTAGRAM_LIKES_LK_VKSERFING);

        // delta should be < 5 seconds
        $ts = Carbon::parse(data_get($slot, 'net_cost.auto_timestamp'));
        $delta = Carbon::now()->diffInSeconds($ts); // returns positive int
        $this->assertLessThan(5, $delta);

        // value changed
        $this->assertEquals(
            (VkserfingFake::CHARGE / $count) * $amount, data_get($slot, 'net_cost.auto'));
    }

    /** @dataProvider provider1 */
    public function testAutoModeShouldNotUpdateNewTimestamp($netCost, $amount, $count)
    {
        $strTimestamp = Carbon::now()->toString();

        // count without mods
        $this->setConfig([
            [
                'name' => Slots::INSTAGRAM_LIKES_LK_VKSERFING,
                'service_class' => AVkserfing::class,
                'order' => 1,
                'min' => 5,
                'max' => 500,
                'remote_params' => [
                    'type' => 'instagram_like',
                ],
                "net_cost" => [
                    "amount" => $amount,
                    "local" => 0,
                    'mode' => AbstractService::NET_COST_AUTO,
                    'auto' => $netCost,
                    'auto_timestamp' => $strTimestamp,
                ],
            ]
        ]);

        $this->travel(10)->minutes();

        $this->createOrder([
            'tag' => UserService::INSTAGRAM_LIKES_LK,
            'count' => $count,
            'api_token' => $this->user->api_token,
            'link' => self::link,
        ])
            ->assertJson(['status' => 'success']);

        $chunk = Chunk::findOrFail(1);
        $this->assertEquals([
            'slot' => Slots::INSTAGRAM_LIKES_LK_VKSERFING,
            'link' => self::link,
            'count' => $count,
            'charge' => $netCost * $count / $amount, // full order price
        ], $chunk->details);

        $slot = Slots::getConfig(Slots::INSTAGRAM_LIKES_LK_VKSERFING);

        // should not update timestamp
        $this->assertEquals(
            $strTimestamp, data_get($slot, 'net_cost.auto_timestamp'));

        // auto value not changed
        $this->assertEquals($netCost, data_get($slot, 'net_cost.auto'));
    }

    /** @dataProvider provider1 */
    public function testDisabledMode($netCost, $amount, $count)
    {
        // count without mods
        $this->setConfig([
            [
                'name' => Slots::INSTAGRAM_LIKES_LK_VKSERFING,
                'service_class' => AVkserfing::class,
                'order' => 1,
                'min' => 5,
                'max' => 500,
                'remote_params' => [
                    'type' => 'instagram_like',
                ],
                "net_cost" => [
                    "amount" => $amount,
                    "local" => 100,
                    'mode' => AbstractService::NET_COST_DISABLED,
                    'auto' => 500,
                    'auto_timestamp' => null,
                ],
            ]
        ]);

        $this->createOrder([
            'tag' => UserService::INSTAGRAM_LIKES_LK,
            'count' => $count,
            'api_token' => $this->user->api_token,
            'link' => self::link,
        ])
            ->assertJson(['status' => 'success']);

        $chunk = Chunk::findOrFail(1);
        $this->assertEquals([
            'slot' => Slots::INSTAGRAM_LIKES_LK_VKSERFING,
            'link' => self::link,
            'count' => $count,
            'charge' => 0.0,
        ], $chunk->details);
    }
}
