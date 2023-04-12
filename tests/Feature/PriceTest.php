<?php

namespace Tests\Feature;

use App\Domain\Models\CompositeOrder;
use App\Domain\Models\Slots;
use App\Domain\Services\AbstractService;
use App\Domain\Services\Nakrutka\ANakrutka;
use App\Domain\Services\TestService1;
use App\Domain\Splitters\DefaultSplitter;
use App\PremiumStatus;
use App\Role\UserRole;
use App\Services\MoneyService;
use App\Transaction;
use App\User;
use App\UserService;
use App\USPrice;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\TF\TFHelpers;

class PriceTest extends TestCase
{
    use DatabaseMigrations;

    public $admin;
    public $user;
    public $us;
    public MoneyService $money;

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

        USPrice::where('tag', UserService::INSTAGRAM_LIKES_LK)->update([
            Transaction::CUR_RUB => [
                1 => 0.1,
                1000 => 0.09,
                5000 => 0.08,
                10000 => 0.07,
                25000 => 0.06,
                50000 => 0.05,
                100000 => 0.04,
            ],
        ]);

        UserService::tag(UserService::INSTAGRAM_LIKES_LK)->update([
                'splitter' => DefaultSplitter::class,
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
                    ]
                ]
            ]
        );

        $this->us = UserService::where('tag', UserService::INSTAGRAM_LIKES_LK)->first();
        $this->money = resolve(MoneyService::class);
    }

    public function testPriceFor1Order()
    {
        $price = $this->us->getPrice(1);
        $this->assertEquals(0.1, $price);
    }

    public function testPriceFor2Orders()
    {
        $price = $this->us->getPrice(2);

        $this->assertEquals(0.1, $price);
    }

    public function testPriceFor1200Orders()
    {
        $price = $this->us->getPrice(1200);

        $this->assertEquals(0.09, $price);
    }

    public function testPriceFor29000Orders()
    {
        $price = $this->us->getPrice(29000);

        $this->assertEquals(0.06, $price);
    }

    public function testPriceFor55000Orders()
    {
        $price = $this->us->getPrice(55000);

        $this->assertEquals(0.05, $price);
    }

    public function testPriceFor100001Orders()
    {
        $price = $this->us->getPrice(100001);

        $this->assertEquals(0.04, $price);
    }

    public function testLikesAndSubsHaveDifferentPrices()
    {
        USPrice::where('tag', UserService::INSTAGRAM_LIKES_LK)->update([
            Transaction::CUR_RUB => [
                1 => 1,
                1000 => 2,
                5000 => 3,
                10000 => 4,
            ]
        ]);

        USPrice::where('tag', UserService::INSTAGRAM_SUBS_LK)->update([
            Transaction::CUR_RUB => [
                1 => 2,
                1000 => 3,
                5000 => 4,
                10000 => 5,
            ]
        ]);

        $likes = UserService::where('tag', UserService::INSTAGRAM_LIKES_LK)->first();
        $this->assertNotNull($likes);

        $subs = UserService::where('tag', UserService::INSTAGRAM_SUBS_LK)->first();
        $this->assertNotNull($subs);

        $n = 1001;
        $this->assertNotEquals($likes->getPrice($n), $subs->getPrice($n));
        $this->assertEquals(2, $likes->getPrice($n));
        $this->assertEquals(3, $subs->getPrice($n));
    }

    public function testLikesPriceForBasicPremiumStatus()
    {
        $user = User::factory()->create();
        $this->assertEquals('Базовый', $user->premiumStatus->name);
        $user->giveMoney(1000, Transaction::CUR_RUB);

        $response = $this->post('/api/c_orders', [
            'tag'           => UserService::INSTAGRAM_LIKES_LK,
            'link'          => 'http://',
            'count'         => 1001,
            'api_token'     => $user->api_token,
            'cur'           => Transaction::CUR_RUB,
            'country_value' => 'RU',
        ]);
        $response->assertStatus(200);

        $order = CompositeOrder::first();
        $cost = 1001 * 0.09; // prices updated in setup

        $this->assertEquals($cost, $order->params['cost']);

        $balance = $this->money->getUserBalance($user, Transaction::CUR_RUB);
        $this->assertEquals(1000 - $cost, $balance);
    }

    public function testLikesPriceForPersonalPremiumStatus()
    {
        $user = User::factory()->create()->giveMoney(1000, Transaction::CUR_RUB);
        $status = PremiumStatus::where('name', 'Персональный')->first();
        $user->premium_status_id = $status->id;
        $user->save();

        $user->refresh();
        $this->assertEquals('Персональный', $user->premiumStatus->name);

        $this->post('/api/c_orders', [
            'tag'           => UserService::INSTAGRAM_LIKES_LK,
            'link'          => 'http://',
            'count'         => 1001,
            'api_token'     => $user->api_token,
            'force_cur'     => Transaction::CUR_RUB,
            'country_value' => 'RU',
        ])->assertStatus(200);

        $order = CompositeOrder::first();
        $countCost = 1001 * 0.09;
        $baseCost = 1001 * 0.1;
        $personalCost = $baseCost * 0.95;
        $cost = round(min($countCost, $personalCost), 2, PHP_ROUND_HALF_DOWN);

        $this->assertEquals($cost, $order->params['cost']);

        $roundedBalance = round(1000 - $cost, 2, PHP_ROUND_HALF_DOWN);

        $userBalance = round($this->money->getUserBalance($user, Transaction::CUR_RUB), 2, PHP_ROUND_HALF_DOWN);

        $this->assertEquals($roundedBalance, $userBalance);
    }
}
