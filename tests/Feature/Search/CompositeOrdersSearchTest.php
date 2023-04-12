<?php

namespace Tests\Feature\Search;

use App\Role\UserRole;
use App\Transaction;
use App\User;
use Database\Seeders\TestOrdersSeeder as S;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\TF\TFHelpers;

class CompositeOrdersSearchTest extends TestCase
{
    use DatabaseMigrations;

    public $admin;
    public $user1;
    public $user2;
    public $user3;

    public function setUp(): void
    {
        parent::setUp();
        TFHelpers::runTestSeeders();

        (new S())->run();

        $this->admin = User::factory()->create([
            'roles' => [UserRole::ROLE_ADMIN],
        ]);

        $this->user1 = User::find(S::USER_1_ID);
        $this->user2 = User::find(S::USER_2_ID);
        $this->user3 = User::find(S::USER_3_ID);
    }

    private function adminSearch($params)
    {
        $res = $this->withToken($this->admin->api_token)
                    ->get(route('admin.search', $params));

        $this->assertEquals('success', $res->json('status'));

        return $res;
    }

    private function userSearch($params)
    {
        $res = $this->withToken($this->user1->api_token)
            ->get(route('user.search', $params));

        $this->assertEquals('success', $res->json('status'));

        return $res;
    }

    public function adminSearchProvider(): array
    {
        return [
            [ ['link' => S::LINK_1], [1] ],
            [ ['link' => S::LOGIN_2], [5] ],
            [ ['link' => S::LOGIN_3], [7, 6] ], // login in link
            [ ['cost' => 100.00], [7, 6, 5, 4, 1] ],
            [ ['cur' => Transaction::CUR_RUB], [11, 10, 9, 8, 3, 2, 1] ],
            [ ['cost' => 100.00,
               'cur' => Transaction::CUR_RUB], [1] ],
            [ ['date_from' => carbon_parse('06 aug 2021')], [11, 10, 9, 8, 7, 6] ],
            [ ['date_to' => carbon_parse('02 aug 2021')], [2, 1] ],
            [ ['date_from' => carbon_parse('03 aug 2021'),
               'date_to' => carbon_parse('05 aug 2021')], [5, 4, 3] ],
            [ ['date_from' => carbon_parse('03 aug 2021'),
               'date_to' => carbon_parse('03 aug 2021')], [3] ],
            [ ['username' => 'om_xxx'], [7, 5, 3, 1] ], // part
            [ ['email' => 'yyy@smmtouch.'], [6, 4, 2] ], // part
            [ ['tag' => S::TAG_1], [2, 1] ],
            [ ['statuses' => 'STATUS_CANCELED'], [9, 7] ],
            [ ['statuses' =>
               'STATUS_PAUSED STATUS_PARTIAL_COMPLETED STATUS_RUNNING'],
                [8, 5, 3, 1] ],
            [ ['cost_to' => 99.99], [2] ],
            [ ['cost_from' => 100.01,
               'cost_to' => 100.01], [3] ],
            [ ['platform' => 'Tiktok'], [2, 1] ],
            [ ['date_from' => '2021-08-15T21:00:00.000Z', // 2021-08-16T00:00:00.000+3 or 2021-08-15T21:00:00.000Z
               'date_to' => '2021-08-16T20:59:59.000Z'], [10, 9] ], // 2021-08-17T00:00:00.000+3 or 2021-08-16T21:00:00.000Z
            // should return orders from 21-08-16 only
        ];
    }

    public function userSearchProvider()
    {
        return [
            [ ['link' => S::LINK_WITH_LOGIN_3], [7] ],
            [ ['link' => S::LOGIN_2], [5] ],
            [ ['link' => S::LOGIN_3], [7] ], // login in link
            [ ['cost' => 100.00], [7, 5] ],
            [ ['date_from' => carbon_parse('06 aug 2021')], [7] ],
            [ ['date_to' => carbon_parse('02 aug 2021')], [] ],
            [ ['date_from' => carbon_parse('03 aug 2021'),
                'date_to' => carbon_parse('05 aug 2021')], [5] ],
            [ ['date_from' => carbon_parse('05 aug 2021'),
                'date_to' => carbon_parse('05 aug 2021')], [5] ],
            [ ['tag' => S::TAG_3], [7] ],
            [ ['statuses' => 'STATUS_CANCELED'], [7] ],
            [ ['statuses' =>
                'STATUS_PAUSED STATUS_PARTIAL_COMPLETED STATUS_RUNNING'],
                [5] ],
//            [ [], [] ],
//            [ [], [] ],
        ];
    }

    /** @dataProvider adminSearchProvider */
    public function testAdminSearch(array $params, array $ids)
    {
        $res = $this->adminSearch($params);
//        echo json_encode($res->json()) . PHP_EOL;
        $this->assertEquals($ids, $res->json('data.items.*.id'));
    }

    public function testAdminSearchWithoutFilters()
    {
        $res = $this->adminSearch([ 'limit' => 1 ]);
        $this->assertCount(1, $res->json('data.items'), 'Must be 1 order');
    }

    /** @dataProvider userSearchProvider */
    public function testUserSearch($params, $ids)
    {
        $res = $this->userSearch($params);
        $this->assertEquals($ids, $res->json('data.items.*.id'));
    }

    public function testFirstOrderSearch()
    {
        $res = $this->adminSearch(['limit' => 1]);
//        $this->assertEquals('2021-07-31T21:00:00.000000Z', $res->json('data.first_order'));

        $this->assertEquals(
            Carbon::parse('2021-08-01')->toISOString(),
            $res->json('data.first_order')
        );
    }

    public function testUserFirstOrderSearch()
    {
        $res = $this->userSearch(['limit' => 1]);
//        $this->assertEquals('2021-07-31T21:00:00.000000Z', $res->json('data.first_order'));

        $this->assertEquals(
            Carbon::parse('2021-08-01')->toISOString(),
            $res->json('data.first_order')
        );
    }

    public function testUserNoFirstOrderSearch()
    {
        $res = $this->withToken($this->user3->api_token)
            ->get(route('user.search'));

        $this->assertCount(0, $res->json('data.items'));
        $this->assertNull($res->json('data.first_order'));
    }

//    public function testAdminSearchTemp()
//    {
//        $params = ['cost_to' => 99.99];
//        $ids = [7];
//
//        $res = $this->withToken($this->admin->api_token)
//            ->get(route('admin.search', $params));
//
//        $this->assertEquals('success', $res->json('status'));
//        $this->assertEquals($ids, $res->json('data.items.*.id'));
//    }
}
