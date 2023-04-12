<?php

namespace Tests\Feature\Splitters;

use App\Domain\Models\Chunk;
use App\Domain\Models\Slots;
use App\Domain\Services\VkserfingAuto\AVkserfingAuto;
use App\Transaction;
use App\User;
use App\UserService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\TF\TFHelpers;

class VkSerfSplitterTest extends TestCase
{
    use DatabaseMigrations;

    const LOGIN = 'buzova86';
    const LINK = 'https://www.tiktok.com/@' . self::LOGIN;

    public $user;

    public function setUp(): void
    {
        parent::setUp();

        TFHelpers::runTestSeeders();

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

    public function testCreateOrder()
    {
        $res = $this->createOrder([
            'api_token' => $this->user->api_token,
            'tag' => UserService::TIKTOK_AUTO_LIKES_LK,

            'login' => self::LOGIN,
            'count' => 100,
            'posts' => 5,
        ]);

        $res->assertJson(['status' => 'success']);
        $chunk = Chunk::first();
        $this->assertEquals(AVkserfingAuto::class, $chunk->service_class);
        $this->assertEquals([
            'slot' => Slots::TIKTOK_AUTO_LIKES_LK_VKSERFING_AUTO,
            'link' => self::LINK,
            'count' => 500, // total likes
            'charge' => (8 / 100) * 500,
        ], $chunk->details);
        $this->assertEquals([
            'token' => 'hidden',
            'type' => 'tiktok_automatic_like',
            'link' => self::LINK,
            'status' => 'on',
            'amount_users_limit' => 100,
            'amount_automatic_records_limit' => 5,
        ], $chunk->add_request);
    }
}
