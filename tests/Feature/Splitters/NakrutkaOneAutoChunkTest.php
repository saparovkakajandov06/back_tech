<?php

namespace Tests\Feature\Splitters;

use App\Domain\Models\Chunk;
use App\Domain\Models\Slots;
use App\Domain\Services\NakrutkaAuto\ANakrutkaAuto;
use App\Transaction;
use App\User;
use App\UserService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\TF\TFHelpers;

class NakrutkaOneAutoChunkTest extends TestCase
{
    use DatabaseMigrations;

    const LOGIN = 'chinesekitchenlbk';
    const LINK = 'https://instagram.com/' . self::LOGIN;

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
            'tag' => UserService::INSTAGRAM_AUTO_VIEWS_LK,

            'login' => self::LOGIN,
            'min' => 100,
            'max' => 100,
            'posts' => 5,
        ]);
        $res->assertJson(['status' => 'success']);
        $chunk = Chunk::first();
        $this->assertEquals(ANakrutkaAuto::class, $chunk->service_class);
        $this->assertEquals([
            'slot' => Slots::INSTAGRAM_AUTO_VIEWS_LK_NAKRUTKA_AUTO,
            'link' => self::LINK,
            'count' => 500,
            'charge' => 3.0 // local 0.6 per 100,
        ], $chunk->details);
        $this->assertEquals([
            'key' => 'hidden',
            'action' => 'add',
            'service' => 15,
            'username' => self::LOGIN,
            'min' => 100,
            'max' => 100,
            'posts' => 5,
            'delay' => 0,
        ], $chunk->add_request);
    }
}
