<?php

namespace Tests\Feature;

use App\Role\UserRole;
use App\Services\MoneyService;
use App\Transaction;
use App\User;
use App\Withdraw;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\TF\TFHelpers;

class WithdrawalTest extends TestCase
{
    use DatabaseMigrations;

    public $admin;
    public $user;
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

        $this->money = resolve(MoneyService::class);
    }

    public function testNotAuthenticatedUserCanNotWithdraw()
    {
        $response = $this->post('/api/withdrawal');
        $response->assertStatus(401);
        $response->assertJson([
            'status' => 'error',
            'error' => AuthenticationException::class,
            'message' => __('s.unauthenticated'),
        ]);
    }

    public function testGiveMoney()
    {
        $this->assertEquals(0.0, $this->money->getUserBalance($this->user, 'RUB'));
        $this->user->giveMoney(100.0, 'RUB');
        $this->assertEquals(100.0, $this->money->getUserBalance($this->user, 'RUB'));

    }

    public function testMakeWithdrawal()
    {
        $this->user->giveMoney(100.0, 'RUB');

        $response = $this->post('/api/withdrawal',
            [
                'amount' => 1.00,
                'cur' => 'RUB',
                'withdraw_method' => 1,
                'wallet_number' => rand(999, 999999),
            ],
            [
                'Authorization' => 'Bearer '.$this->user->api_token
            ]);
        $response->assertStatus(200)
            ->assertJson(['status' => 'success']);

        $balance = $this->money->getUserBalance($this->user, 'RUB');
        $this->assertEquals(99.0, $balance);
        $this->assertEquals(1, Withdraw::count());

        $w = Withdraw::first();
        $this->assertEquals(Withdraw::BANK_CARD, $w->type);
    }

    public function testCanNotWithdrawIfNoMoney()
    {
        $response = $this->post('/api/withdrawal',
            [
                'amount' => 1.00,
                'cur' => 'RUB',
                'withdraw_method' => 1,
                'wallet_number' => rand(999, 999999),
            ],
            [
                'Authorization' => 'Bearer '.$this->user->api_token
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status'    => 'error',
                'message'   => Transaction::NOT_ENOUGH_FUNDS,
            ]);
    }
}