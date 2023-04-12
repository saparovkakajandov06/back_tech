<?php

namespace Tests\Feature;

use App\Exceptions\NonReportable\BadCurrencyException;
use App\Role\UserRole;
use App\Services\MoneyService;
use App\Transaction;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\TF\TFHelpers;

class MoneyServiceTest extends TestCase
{
    use DatabaseMigrations;

    public $admin;
    public $user;
    protected MoneyService $m;

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

    public function testNewUserHasNoMoney()
    {
        $user = User::factory()->create();
        $balance = $this->m->getUserBalance($user->id, 'RUB');
        $this->assertEquals(0.00, $balance);
        $balance = $this->m->getUserBalance($user->id, 'USD');
        $this->assertEquals(0.00, $balance);
    }

    public function testCanGiveMoneyToUser()
    {
        $user = User::factory()->create();
        $amountRUB = 1000.01;
        $user->giveMoney($amountRUB, Transaction::CUR_RUB);

        $balanceRUB = $this->m->getUserBalance($user, Transaction::CUR_RUB);
        $this->assertEquals($amountRUB, $balanceRUB);

        $amountUSD = 101.22;
        $user->giveMoney($amountUSD, Transaction::CUR_USD);
        $balanceUSD = $this->m->getUserBalance($user, Transaction::CUR_USD);
        $this->assertEquals($amountUSD, $balanceUSD);
    }

    public function testUnknownCurrency()
    {
        $this->expectException(BadCurrencyException::class);

        $user = User::factory()->create();
        $amountRUB = 1000.01;
        $user->giveMoney($amountRUB, 'VERY_BAD_CURRENCY');
    }
}