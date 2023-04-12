<?php

namespace Tests\Feature\Search;

use App\Services\Search\TransactionsSearchService;
use App\Transaction;
use App\User;
use Database\Seeders\TestTransactionsSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\TF\TFHelpers;

class TransactionsSearchTest extends TestCase
{
    use DatabaseMigrations;

    public $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        (new TestTransactionsSeeder())->run($this->user);

        TFHelpers::runTestSeeders();
    }

    public function testUserSearchByDateAndCurrency()
    {
        $res = $this->withToken($this->user->api_token)
            ->get(route('user.transactions.search', [
                'date_from' => '2020-08-01',
                'date_to' => '2020-08-05',
                'cur' => Transaction::CUR_RUB,
            ]));

        $this->assertEquals([5, 4, 3, 2], $res->json('data.items.*.id'));
    }

    public function testUserSearchByTypes()
    {
        $res = $this->withToken($this->user->api_token)
            ->get(route('user.transactions.search', [
                'types' => 'INFLOW_CREATE INFLOW_PAYMENT'
            ]));

        $this->assertEquals([10, 8, 7, 5, 2], $res->json('data.items.*.id'));
    }

    public function testInsertTotalsData()
    {
        $zeros = [
            [ 'type' => 'b', 'amount' => 0.00 ],
            [ 'type' => 'a', 'amount' => 0.00 ],
        ];

        $res = Transaction::insertTotalsData($zeros, [
            [ 'type' => 'b', 'amount' => 1.00 ],
        ]);

        $this->assertEquals([
            [ 'type' => 'a', 'amount' => 0.00 ],
            [ 'type' => 'b', 'amount' => 1.00 ],
        ], $res);
    }

    public function testUserMoneyTotalsByType()
    {
        // not api version
        $res = $this->withToken($this->user->api_token)
            ->get('api/user/transactions/totals?cur=RUB');

        $data = [
            [
                'amount' => 16.00 + 17.00,
                'type' => Transaction::INFLOW_CREATE,
            ],
            [
                'amount' => 18.00,
                'type' => Transaction::INFLOW_OTHER,
            ],
            [
                'amount' => 11.00 + 14.00,
                'type' => Transaction::INFLOW_PAYMENT,
            ],
            [
                'amount' => 12.00 + 15.00,
                'type' => Transaction::INFLOW_REF_BONUS,
            ],
            [
                'amount' => 13.00,
                'type' => Transaction::INFLOW_REFUND,
            ],
        ];

        $totals = Transaction::withZeros($data);

        $this->assertEquals($totals, $res->json('data'));
    }
}
