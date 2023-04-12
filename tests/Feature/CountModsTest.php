<?php

namespace Tests\Feature;

use App\Domain\Models\Chunk;
use App\Domain\Models\Slots;
use App\Domain\Services\AbstractService;
use App\Domain\Services\Nakrutka\ANakrutka;
use App\Role\UserRole;
use App\Transaction;
use App\User;
use App\UserService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\TF\TFHelpers;

class CountModsTest extends TestCase
{
    use DatabaseMigrations;

    public $admin;
    public $user;

    public function setUp(): void
    {
        parent::setUp();
//        TFHelpers::runCommonSeeders();
        TFHelpers::runTestSeeders();

        $this->admin = User::factory()->create([
            'roles' => [UserRole::ROLE_ADMIN],
        ]);

        $this->user = User::factory()->create();
        $this->user->giveMoney(1000, Transaction::CUR_RUB);
    }

    public function createOrder(array $params)
    {
        $default = [
            'link'              => 'http://link_to',
            'count'             => '0',
            'tag'               => 'SOME_BAD_TAG',
            'api_token'         => null,
            'region_value'      => 'CIS',
            'force_cur'         => Transaction::CUR_RUB,
            'country_value'     => 'RU',
        ];

        return $this->post(
            '/api/c_orders',
            array_merge($default, $params)
        );
    }

    public function testCountExtraPercent()
    {
        UserService::tag(UserService::INSTAGRAM_LIKES_LK)->update([
                'config' => [
                    [
                        'name' => Slots::INSTAGRAM_LIKES_LK_NAKRUTKA,
                        'service_class' => ANakrutka::class,
                        'order' => 1,
                        'min' => 1,
                        'max' => 10000,
                        'remote_params' => [
                            'service' => 81,
                        ],
                        'count_extra_percent' => 10,
                    ]
                ],
            ]
        );

        $this->assertEquals(0, Chunk::count());

        $this->createOrder([
            'api_token' => $this->user->api_token,
            'tag' => UserService::INSTAGRAM_LIKES_LK,
            'link' => 'http://link',
            'count' => 100,
        ]);

        $this->assertEquals(1, Chunk::count());
        $this->assertEquals(100, Chunk::first()->details['count']);
        // service sends 110, but chunk has 100
    }

    public function testCountMin()
    {
        UserService::tag(UserService::INSTAGRAM_LIKES_LK)
            ->firstOrFail()
            ->update([
                    'config' => [
                        [
                            'name' => Slots::INSTAGRAM_LIKES_LK_NAKRUTKA,
                            'service_class' => ANakrutka::class,
                            'order' => 1,
                            'min' => 1,
                            'max' => 10000,
                            'remote_params' => [
                                'service' => 81,
                            ],
                            'count_min' => 500,
                        ]
                    ],
                ]
            );

        $this->createOrder([
            'api_token' => $this->user->api_token,
            'tag' => UserService::INSTAGRAM_LIKES_LK,
            'link' => 'http://link',
            'count' => 100,
        ]);

        $this->assertEquals(1, Chunk::count(), 'Should be 1 chunk');
        $chunk = Chunk::latest()->firstOrFail();

        $this->assertEquals(100, $chunk->details['count']);
    }
}