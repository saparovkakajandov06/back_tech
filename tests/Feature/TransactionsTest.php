<?php

namespace Tests\Feature;

use App\Services\MoneyService;
use App\Transaction;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\TF\TFHelpers;

class TransactionsTest extends TestCase
{
    use DatabaseMigrations;

    public $user;
    public MoneyService $money;

    public function setUp(): void
    {
        parent::setUp();
//        TFHelpers::runCommonSeeders();
        TFHelpers::runTestSeeders();

        $this->user = $this->registerAUser();
        $this->money = resolve(MoneyService::class);
    }

    public function registerAUser(array $params = [])
    {
        $password = '123456';

        $default = [
            'login' => 'test_reg_' . Str::random(6),
            'email' => Str::random(6) . '@smmtouch.store',
            'password' => $password,
            'password_confirm' => $password,
            'lang' => User::LANG_RU,
            'cur' => Transaction::CUR_RUB,
        ];
        $params = array_merge($default, $params);

        $res = $this->post('api/register', $params)->assertStatus(200);
        $token = $res->json('data.token');
        $this->assertNotNull($token);
        $user = User::where('api_token', $token)->firstOrFail();
        return $user;
    }

    public function testNewUserHasNoTransactions()
    {
        $this->assertEmpty($this->user->transactions);
    }

    public function testNewUserHasZeroBalance()
    {
        $this->assertEquals(0.0, $this->money->getUserBalance($this->user, 'RUB'));
        $this->assertEquals(0.0, $this->money->getUserBalance($this->user, 'USD'));
        $this->assertEquals(0.0, $this->money->getUserBalance($this->user, 'EUR'));
        $this->assertEquals(0.0, $this->money->getUserBalance($this->user, 'TRY'));
        $this->assertEquals(0.0, $this->money->getUserBalance($this->user, 'BRL'));
        $this->assertEquals(0.0, $this->money->getUserBalance($this->user, 'UAH'));
    }

    public function testBasicTransaction()
    {
        $this->money->inflow($this->user, 1.00, 'RUB', Transaction::INFLOW_TEST);
        $this->assertCount(1, $this->user->transactions);
        $this->assertEquals(1.00, $this->money->getUserBalance($this->user, 'RUB'));
    }

    public function testTransactions()
    {
        $this->money->inflow($this->user, 1.00, 'RUB', Transaction::INFLOW_TEST);
        $this->money->inflow($this->user, 2.00, 'RUB', Transaction::INFLOW_TEST);

        $this->money->outflow($this->user, -1.00, 'RUB', Transaction::OUTFLOW_TEST);

        $this->assertCount(3, $this->user->transactions);
        $balance = $this->money->getUserBalance($this->user, 'RUB');
        $this->assertEquals(2.00, $balance);
    }

    public function testTransactionsTotals()
    {
        $response = $this->withToken($this->user->api_token)
            ->get('/api/user/transactions/totals?cur=RUB');

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
    }

    public function testTransactionsX()
    {
        $response = $this
            ->withHeaders(['Authorization' => 'Bearer '.$this->user->api_token])
            ->get('/api/user/transactions');

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
    }
}
