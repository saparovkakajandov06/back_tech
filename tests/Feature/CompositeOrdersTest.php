<?php

namespace Tests\Feature;

use App\Domain\Models\Chunk;
use App\Domain\Models\CompositeOrder;
use App\Domain\Models\Slots;
use App\Domain\Services\AbstractService;
use App\Domain\Services\Fake\AFake;
use App\Domain\Services\Fake\AFake2;
use App\Domain\Services\Nakrutka\ANakrutka;
use App\Domain\Services\Socgress\ASocgress;
use App\Domain\Services\Vtope\AVtope;
use App\Exceptions\NonReportable\PipelineValidationException;
use App\Role\UserRole;
use App\Services\MoneyService;
use App\Transaction;
use App\User;
use App\UserService;
use App\USPrice;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;
use Tests\TF\TFHelpers;

class CompositeOrdersTest extends TestCase
{
    use DatabaseMigrations;

    public $admin;
    public $user;
    public MoneyService $money;

    public function setUp(): void
    {
        parent::setUp();
        TFHelpers::runTestSeeders();

        $this->admin = User::factory()->create([
            'roles' => [UserRole::ROLE_ADMIN],
        ]);

        $this->user = User::factory()->create();
        $this->user->giveMoney(2000.0, Transaction::CUR_RUB);

        $this->money = resolve(MoneyService::class);
    }

    public function testNotAuthorizedUserCanNotMakeOrder()
    {
        $res = $this->post('/api/c_orders');
        $res->assertStatus(401)
            ->assertJson(['status' => 'error']);
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

    public function testCreate3InstagramLikes()
    {
        UserService::where('tag', UserService::INSTAGRAM_LIKES_LK)
            ->firstOrFail()
            ->update([
                'config' => [
                    [
                        'name' => Slots::INSTAGRAM_LIKES_LK_NAKRUTKA,
                        'service_class' => ANakrutka::class,
                        'order' => 1,
                        'min' => 1,
                        'max' => 1,
                        'remote_params' => [
                            'service' => 81,
                        ],
                    ],
                    [
                        'name' => Slots::INSTAGRAM_LIKES_LK_VTOPE,
                        'service_class' => AVtope::class,
                        'order' => 2,
                        'min' => 1,
                        'max' => 1,
                        'remote_params' => [
                            'method' => 'add',
                            'service' => 'k',
                            'type' => 'like',
                        ]
                    ],
                    [
                        'name' => Slots::INSTAGRAM_LIKES_LK_SOCGRESS,
                        'service_class' => ASocgress::class,
                        'order' => 3,
                        'min' => 1,
                        'max' => 1,
                        'remote_params' => [
                            'service_id' => 33,
                            'network' => 'instagram',
                            'speed' => 'slow',
                        ]
                    ],
                ]
            ]);

        $this->createOrder([
            'tag' => UserService::INSTAGRAM_LIKES_LK,
            'count' => '3',
            'api_token' => $this->user->api_token,
        ])
        ->assertJson(['status' => 'success']);

        $this->assertCount(1, CompositeOrder::all());
        $this->assertCount(3, Chunk::all());

        Chunk::all()->each(
            fn($chunk) => $this->assertEquals(1, $chunk->details['count'])
        );
    }

    public function testCreateFakeOrder()
    {
        $this->createOrder([
            'api_token' => $this->user->api_token,
            'tag' => UserService::FAKE_SERVICE_LK,
            'count' => '2000',
            'target1' => '100',
            'target2' => '100',
        ])->assertJson(['status' => 'success']);

        $this->assertCount(1, CompositeOrder::all());
        $this->assertCount(2, Chunk::all());

        Chunk::all()->each(
            fn($chunk) => $this->assertEquals(1000, $chunk->details['count'])
        );
    }

    public function testReconfigureFakeService()
    {
        UserService::where('tag', UserService::FAKE_SERVICE_LK)
            ->firstOrFail()
            ->update([
                'config' => [
                    [
                        'name' => Slots::FAKE_SERVICE_LK_FAKE_1,
                        'service_class' => AFake::class,
                        'order' => 1,
                        'min' => 1,
                        'max' => 100,
                        'target' => 'target1',
                    ],
                    [
                        'name' => Slots::FAKE_SERVICE_LK_FAKE_2,
//                    'service_class' => AFake2::class,
                        'service_class' => AFake::class,
                        'order' => 1,
                        'min' => 1,
                        'max' => 100,
                        'target' => 'target2',
                    ],
                ]
            ]);

        $this->createOrder([
            'api_token' => $this->user->api_token,
            'tag' => UserService::FAKE_SERVICE_LK,
            'count' => '200',
            'target1' => '100',
            'target2' => '100',
        ]);

        $this->assertCount(1, CompositeOrder::all());
        $this->assertCount(2, Chunk::all());

        Chunk::all()->each(
            fn($chunk) => $this->assertEquals(100, $chunk->details['count'])
        );
    }

    public function testTagIsRequired()
    {
        $this->createOrder([
            'api_token' => $this->user->api_token,
            'link' => 'http://link_to',
            'count' => '16',
        ])
        ->assertStatus(404)
        ->assertJson([
            'status' => 'error',
            'error' => NotFoundHttpException::class,
        ]);
    }

    public function testNoChunksForNonExistentService()
    {
        $response = $this->createOrder([
            'api_token' => $this->user->api_token,
            'tag' => 'SOME_BAD_TAG',
            'count' => '16',
        ]);

        $response->assertStatus(404);
        $response->assertJson([
            'status' => 'error',
            'error' => NotFoundHttpException::class,
        ]);

        $this->assertCount(0, CompositeOrder::all());
        $this->assertCount(0, Chunk::all());
    }

    public function testShouldNotCreateChunksWithZeroCount()
    {
        $count = 20;

        UserService::where('tag', UserService::INSTAGRAM_LIKES_LK)->update([
            'config' => [
                [
                    'name' => Slots::INSTAGRAM_LIKES_LK_NAKRUTKA,
                    'service_class' => ANakrutka::class,
                    'order' => 1,
                    'min' => 1,
                    'max' => $count + 80,
                    'remote_params' => [
                        'service_id' => 3,
                    ],
                    'count_extra_percent' => 10,
                    'net_cost' => [
                        'amount' => 100,
                        'local' => 24.024,
                        'mode' => AbstractService::NET_COST_LOCAL,
                    ],
                    'isEnabled' => true
                ],
                [
                    'name' => Slots::INSTAGRAM_LIKES_LK_VTOPE,
                    'service_class' => AVtope::class,
                    'order' => 2,
                    'min' => 1,
                    'max' => $count + 80,
                    'remote_params' => [
                        'method' => 'add',
                        'service' => 'k',
                        'type' => 'like',
                    ]
                ]
            ]
        ]);

        $response = $this->createOrder([
            'api_token' => $this->user->api_token,
            'tag' => UserService::INSTAGRAM_LIKES_LK,
            'count' => $count,
        ]);

        $response->assertStatus(200);

        $this->assertCount(1, CompositeOrder::all());
        $this->assertCount(1, Chunk::all());

        $this->assertEquals($count, Chunk::first()->details['count']);
    }

    public function testCreateOrdersForDifferentUserServices()
    {
        UserService::tag(UserService::INSTAGRAM_SUBS_LK)->update([
            'config' => [
                [
                    'name' => Slots::INSTAGRAM_SUBS_LK_NAKRUTKA,
                    'service_class' => ANakrutka::class,
                    'order' => 1,
                    'min' => 1,
                    'max' => 100,
                    'remote_params' => [
                        'service_id' => 3,
                    ],
                    'count_extra_percent' => 10,
                    'net_cost' => [
                        'amount' => 100,
                        'local' => 24.024,
                        'mode' => AbstractService::NET_COST_LOCAL,
                    ],
                    'isEnabled' => true
                ]
            ]]);

        UserService::tag(UserService::INSTAGRAM_LIKES_LK)->update([
            'config' => [
                [
                    'name' => Slots::INSTAGRAM_LIKES_LK_VTOPE,
                    'service_class' => AVtope::class,
                    'order' => 1,
                    'min' => 1,
                    'max' => 100,
                    'remote_params' => [
                        'method' => 'add',
                        'service' => 'k',
                        'type' => 'like',
                    ]
                ],
            ]]);

        $this->createOrder([
            'api_token' => $this->user->api_token,
            'tag' => UserService::INSTAGRAM_SUBS_LK,
            'count' => '1',
        ])->assertStatus(200);

        $this->assertCount(1, CompositeOrder::all());
        $this->assertCount(1, Chunk::all());
        $this->assertEquals(UserService::INSTAGRAM_SUBS_LK,
            CompositeOrder::find(1)->userService->tag);

        $this->createOrder([
            'api_token' => $this->user->api_token,
            'tag' => UserService::INSTAGRAM_LIKES_LK,
            'count' => '1',
        ])->assertStatus(200);

        $this->assertCount(2, CompositeOrder::all());
        $this->assertCount(2, Chunk::all());
        $this->assertEquals(UserService::INSTAGRAM_LIKES_LK,
            CompositeOrder::find(2)->userService->tag);
    }

    public function testOrderPayment()
    {
        UserService::where('tag', UserService::INSTAGRAM_LIKES_LK)->update([
            'config' => [
                [
                    'name' => Slots::INSTAGRAM_LIKES_LK_NAKRUTKA,
                    'service_class' => ANakrutka::class,
                    'order' => 1,
                    'min' => 1,
                    'max' => 100,
                    'remote_params' => [
                        'service' => 81,
                    ]
                ],
            ],
        ]);

        USPrice::where('tag', UserService::INSTAGRAM_LIKES_LK)
            ->firstOrFail()
            ->update([
                Transaction::CUR_RUB => [
                    1 => 1,
                    1000 => 2,
                    5000 => 3,
                    10000 => 4,
                ],
            ]);

        $balance = $this->money->getUserBalance($this->user, Transaction::CUR_RUB);

        $response = $this->createOrder([
            'api_token' => $this->user->api_token,
            'tag' => UserService::INSTAGRAM_LIKES_LK,
            'count' => '3',
        ])->json();

        $newId = $response['data']['orders'][0]['id'];
        $order = CompositeOrder::find($newId);

        $this->assertEquals(3.0, $order->params['cost']);
        $this->assertEquals(Transaction::CUR_RUB, $order->params['cur']);

        $newBalance = $this->money
            ->getUserBalance($this->user, Transaction::CUR_RUB);
        $this->assertEquals($balance - 3.0, $newBalance);
    }

    public function testShouldCheckFunds()
    {
        $user = User::factory()->create();
        $balance = $this->money
            ->getUserBalance($user, Transaction::CUR_RUB);
        $this->assertEquals(0.0, $balance);

        $this->withoutExceptionHandling();

        $this->expectException(PipelineValidationException::class);
        $this->expectExceptionMessageMatches('/not enough funds/i');

        $res = $this->createOrder([
            'api_token' => $user->api_token,
            'tag'       => UserService::INSTAGRAM_LIKES_LK,
            'count'     => '3',
        ])->json();

//        $messageFound = preg_match('/not enough funds/i', $res['data']['message']);
//        $this->assertEquals(1, $messageFound);

        $this->assertEquals(0, CompositeOrder::query()->count());
    }
}