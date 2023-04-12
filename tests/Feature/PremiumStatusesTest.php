<?php

namespace Tests\Feature;

use App\PremiumStatus;
use App\Role\UserRole;
use App\Services\MoneyService;
use App\Transaction;
use App\User;
use App\UserService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Tests\TF\TFHelpers;

class PremiumStatusesTest extends TestCase
{
    use DatabaseMigrations;

    public $admin;
    public $user;
    public MoneyService $m;

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

        $this->m = resolve(MoneyService::class);
    }

    protected function getRegisteredUser()
    {
        $res = $this->post('api/register', [
            'login' => 'login123',
            'email' => 'email123@example.com',
            'password' => 'secret',
            'password_confirm' => 'secret',
            'lang' => User::LANG_RU,
            'cur' => Transaction::CUR_RUB,
        ])->assertStatus(200);

        $token = $res->json('data.token');
        $this->assertNotNull($token);
        $user = User::where('api_token', $token)->firstOrFail();
        return $user;
    }

    public function testNewUserShouldHaveZeroPayments()
    {
        $user = $this->getRegisteredUser();

        $this->assertEquals(0,
            $this->m->getPaymentsSum($user, Transaction::CUR_RUB));
        $this->assertEquals(0,
            $this->m->getPaymentsSum($user, Transaction::CUR_USD));
    }

    public function testGetPremiumStatusShouldReturnPremiumStatus()
    {
        $this->assertInstanceOf(PremiumStatus::class, $this->user->premiumStatus);
    }

    public function testNewUserShouldHaveBasicPremiumStatus()
    {
        $this->assertEquals('Базовый', $this->user->premiumStatus->name);
    }

    public function testUserHasPersonalPremiumStatus()
    {
        $this->m->inflow($this->user, 5000, 'RUB',
            Transaction::INFLOW_PAYMENT);
        $this->assertEquals('Персональный', $this->user->premiumStatus->name);
    }

    public function testUserHasPremiumPremiumStatus()
    {
        $this->m->inflow($this->user, 14000, 'RUB', Transaction::INFLOW_PAYMENT);
        $this->m->inflow($this->user, 14000, 'RUB', Transaction::INFLOW_PAYMENT);
        $this->assertEquals('Премиум', $this->user->premiumStatus->name);
    }

    public function testUserHasBloggerPremiumStatus()
    {
        $this->m->inflow($this->user, 14000, 'RUB', Transaction::INFLOW_PAYMENT);
        $this->m->inflow($this->user, 14000, 'RUB', Transaction::INFLOW_PAYMENT);
        $this->m->inflow($this->user, 14000, 'RUB', Transaction::INFLOW_PAYMENT);
        $this->m->inflow($this->user, 14000, 'RUB', Transaction::INFLOW_PAYMENT);

        $this->assertEquals('Блогер', $this->user->premiumStatus->name);
    }

    public function testUserHasElitePremiumStatus()
    {
        $this->m->inflow($this->user, 101000, 'RUB', Transaction::INFLOW_PAYMENT);
        $this->m->outflow($this->user, -100000, 'RUB', Transaction::OUTFLOW_OTHER);

        $this->assertEquals('Элитный', $this->user->premiumStatus->name);
    }

    public function test1000LikesBaseCost()
    {
        $userService = UserService::where('tag', UserService::INSTAGRAM_LIKES_LK)
            ->firstOrFail();

        Auth::login($this->user);

        $cost = $userService->getFinalCost(1000, 'RUB');

        $this->assertEquals(185, $cost);
    }

    public function test1000LikesPremiumCost()
    {
        $userService = UserService::where('tag', UserService::INSTAGRAM_LIKES_LK)
            ->first();

        $this->m->inflow($this->user, 25000, 'RUB', Transaction::INFLOW_PAYMENT);

        Auth::login($this->user);

        $cost = $userService->getFinalCost(1000, 'RUB');

        $countCost = 1000 * 0.185;
        $baseCost = 1000 * 0.19;
        $personalCost = $baseCost * 0.9;
        $this->assertEquals(min($countCost, $personalCost), $cost);
    }

    public function testUserHasPersonalPremiumStatusUSD()
    {
        $this->m->inflow($this->user, 100, 'USD', Transaction::INFLOW_PAYMENT);

        $this->assertEquals('Персональный', $this->user->premiumStatus->name);
    }

    public function testUserHasPremiumPremiumStatusUSD()
    {
        $this->m->inflow($this->user, 500, 'USD', Transaction::INFLOW_PAYMENT);

        $this->assertEquals('Премиум', $this->user->premiumStatus->name);
    }

    public function testUserHasBloggerPremiumStatusUSD()
    {
        $this->m->inflow($this->user, 1, 'USD', Transaction::INFLOW_PAYMENT);
        $this->m->inflow($this->user, 2, 'USD', Transaction::INFLOW_PAYMENT);
        $this->m->inflow($this->user, 997, 'USD', Transaction::INFLOW_PAYMENT);

        $this->assertEquals('Блогер', $this->user->premiumStatus->name);
    }

    public function testUserHasElitePremiumStatusUSD()
    {
        $this->m->inflow($this->user, 2000, 'USD', Transaction::INFLOW_PAYMENT);

        $this->assertEquals('Элитный', $this->user->premiumStatus->name);
    }

    public function testShouldNotResetStatusInSameCurrency()
    {
        $this->m->inflow($this->user, 2000, 'USD', Transaction::INFLOW_PAYMENT);
        $this->m->outflow($this->user, -1, 'USD', Transaction::OUTFLOW_OTHER);

        $this->assertEquals('Элитный', $this->user->premiumStatus->name);
    }

    public function testStatusesHaveDifferentCurrencies()
    {
        $this->m->inflow($this->user, 3999, 'RUB', Transaction::INFLOW_PAYMENT);
        $this->m->inflow($this->user, 99, 'USD', Transaction::INFLOW_PAYMENT);

        $this->assertEquals('Базовый', $this->user->premiumStatus->name);
    }

    public function testShouldNotResetStatusInAnotherCurrency()
    {
        $this->m->inflow($this->user, 2000, 'USD', Transaction::INFLOW_PAYMENT);
        $this->assertEquals('Элитный', $this->user->premiumStatus->name);

        $this->m->inflow($this->user, 1, 'RUB', Transaction::INFLOW_PAYMENT);

        $this->assertEquals('Элитный', $this->user->premiumStatus->name);
    }
}