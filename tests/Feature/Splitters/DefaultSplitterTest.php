<?php

namespace Tests\Feature\Splitters;

use App\Domain\Models\Chunk;
use App\Domain\Models\Slots;
use App\Domain\Services\Everve\AEverve;
use App\Domain\Services\Nakrutka\ANakrutka;
use App\Domain\Services\Socgress\ASocgress;
use App\Domain\Services\Vkserfing\AVkserfing;
use App\Role\UserRole;
use App\Services\MoneyService;
use App\Transaction;
use App\User;
use App\UserService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\TF\TFHelpers;

class DefaultSplitterTest extends TestCase
{
    use DatabaseMigrations;

    const link = 'http://example_link_to';

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

    public function testCreate20InstagramLikes()
    {
        $this->createOrder([
            'tag' => UserService::INSTAGRAM_LIKES_LK,
            'count' => 20,
            'api_token' => $this->user->api_token,
            'link' => self::link,
        ])
            ->assertJson(['status' => 'success']);

//        $this->assertCount(1, CompositeOrder::all());
//        $this->assertCount(4, Chunk::all());

        $chunk = Chunk::find(1);
        $this->assertEquals(AVkserfing::class, $chunk->service_class);
        $this->assertEquals([
            'slot' => Slots::INSTAGRAM_LIKES_LK_VKSERFING,
            'link' => self::link,
            'count' => 5,
            'charge' => 5 * (5 / 100),
        ], $chunk->details);
        $this->assertEquals([
            'token' => 'hidden',
            'link' => self::link,
            'status' => 'on',
            'amount_users_limit' => 5,
            'type' => 'instagram_like'
        ], $chunk->add_request);

        $chunk = Chunk::find(2);
        $this->assertEquals(AEverve::class, $chunk->service_class);
        $this->assertEquals([
            'slot' => Slots::INSTAGRAM_LIKES_LK_EVERVE,
            'link' => self::link,
            'count' => 5,
            'charge' => 5 * (7.1 / 100),
        ], $chunk->details);
        $this->assertEquals([
            'api_key' => 'hidden',
            'order_url' => self::link,
            'order_overall_limit' => 5,
            'clear_prev_stat' => 1,
            'category_id' => 18,
            'order_price' => 0.001,
        ], $chunk->add_request);

        $chunk = Chunk::find(3);
        $this->assertEquals(ASocgress::class, $chunk->service_class);
        $this->assertEquals([
            'slot' => Slots::INSTAGRAM_LIKES_LK_SOCGRESS,
            'link' => self::link,
            'count' => 5,
            'charge' => 0.0,
        ], $chunk->details);
        $this->assertEquals([
            'token' => 'hidden',
            'link' => self::link,
            'count' => 5,
            'network' => 'instagram',
            'service_id' => 33,
            'speed' => 'slow',
        ], $chunk->add_request);

        $chunk = Chunk::find(4);
        $this->assertEquals(ANakrutka::class, $chunk->service_class);
        $this->assertEquals([
            'slot' => SLots::INSTAGRAM_LIKES_LK_NAKRUTKA,
            'link' => self::link,
            'count' => 5,
            'charge' => 0.0,
        ], $chunk->details);
        $this->assertEquals([
            'key' => 'hidden',
            'action' => 'add',
            'service' => 81,
            'link' => self::link,
            'quantity' => 5,
        ], $chunk->add_request);
    }

    public function testGetSlotByName()
    {
        $start = microtime(true);

        $config = Slots::getConfig(Slots::INSTAGRAM_LIKES_LK_VKSERFING);
        $this->assertEquals(Slots::INSTAGRAM_LIKES_LK_VKSERFING, $config['name']);
        $this->assertEquals(AVkserfing::class, $config['service_class']);

        $time_elapsed_secs = microtime(true) - $start;
    }
}
