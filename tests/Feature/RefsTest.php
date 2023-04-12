<?php

namespace Tests\Feature;

use App\Domain\Models\CompositeOrder;
use App\Domain\Models\Slots;
use App\Domain\Services\AbstractService;
use App\Domain\Services\Nakrutka\ANakrutka;
use App\Order;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use App\Role\UserRole;
use App\Services\MoneyService;
use App\Transaction;
use App\User;
use App\UserService;
use App\USPrice;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\TF\TFHelpers;

class RefsTest extends TestCase
{
    use DatabaseMigrations;

    public $admin;
    public $user;
    public MoneyService $money;
    public TransactionRepositoryInterface $transactionRepository;

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

        $this->money = resolve(MoneyService::class);
        $this->transactionRepository = resolve(TransactionRepositoryInterface::class);
    }

    public function testUserCanHaveParentAndRefs()
    {
        $parent = User::factory()->create();
        $ref1 = User::factory()->create(['parent_id' => $parent->id]);
        $ref2 = User::factory()->create(['parent_id' => $parent->id]);

        $this->assertInstanceOf(User::class, $ref1->parent);
        $this->assertInstanceOf(User::class, $ref2->parent);
        $this->assertCount(2, $parent->refs);
    }

    public function testUserMustHaveRefCode()
    {
        $u = User::factory()->create();
        $this->assertNotEmpty($u->ref_code);
    }

    public function testRefBonus()
    {
        UserService::where('tag', UserService::INSTAGRAM_LIKES_LK)->update([
            'config' => [
                [
                    'name' => Slots::INSTAGRAM_LIKES_LK_NAKRUTKA,
                    'service_class' => ANakrutka::class,
                    'order' => 1,
                    'min' => 1,
                    'max' => 100,
                    'remote_params' => [
                        'service' => 88,
                    ],
                    'net_cost' => [
                        'amount' => 100,
                        'local' => 4.187,
                        'mode' => AbstractService::NET_COST_DISABLED,
                        'auto' => 123,
                        'auto_timestamp' => null,
                    ],
                ]
            ],
        ]);

        USPrice::where('tag', UserService::INSTAGRAM_LIKES_LK)->update([
            Transaction::CUR_RUB => [
                1 => 1,
                1000 => 2,
                5000 => 3,
                10000 => 4,
            ],
        ]);

        $parent = User::factory()->create();
        $ref = User::factory()
            ->create(['parent_id' => $parent->id])
            ->giveMoney(100.0, Transaction::CUR_RUB);

        $this->post('/api/c_orders', [
            'link'          => 'http://link.to',
            'count'         => '20',
            'tag'           => UserService::INSTAGRAM_LIKES_LK,
            'api_token'     => $ref->api_token,
            'force_cur'     => Transaction::CUR_RUB,
            'country_value' => 'RU',
        ])->assertStatus(200); // running

        $order = CompositeOrder::latest()->first();
        $order->startUpdate()->complete();

        $bonusTransactions = Transaction::where('type',
            Transaction::INFLOW_REF_BONUS)
            ->get();

        $this->assertCount(1, $bonusTransactions);
    }

    protected function createRefsWithBonuses($parent, $children)
    {
        foreach($children as $name => $total) {
            $ref = User::factory()->create([
                'name' => $name,
                'parent_id' => $parent->id,
            ]);

            $paid = 0;

            while($paid < $total) {
                $amount = rand(1, $total - $paid);

                $parent->transactions()->create([
                    'type'              => Transaction::INFLOW_REF_BONUS,
                    'amount'            => $amount,
                    'cur'               => Transaction::CUR_RUB,
                    'comment'           => 'бонус',
                    'event_id'          => rand(0, 0xFFFFFFFF),
                    'related_user_id'   => $ref->id,
                ]);

                $paid += $amount;
            }
        }
    }

    public function testGetRefBonusesWithNames()
    {
        $parent1 = User::create([
            'id'            => 201,
            'name'          => 'parent1',
            'password'      => bcrypt('secret'),
            'email'         => 'parent1@smm.example.com',
            'roles'         => [UserRole::ROLE_VERIFIED],
            'api_token'     => User::getFreeToken(),
        ]);

        $refs = [
            Str::random(10) => rand(1, 100),
            Str::random(10) => rand(1, 20),
            Str::random(10) => rand(1, 30),
        ];

        $this->createRefsWithBonuses($parent1, $refs);

        $ts = $this->transactionRepository->getRefBonuses($parent1->id);

        foreach ($refs as $name => $total) {
            $item = $ts->where('name', $name)->first();
            $this->assertEquals($total, $item->sum);
        }

        $response = $this->get('/api/user',
            ['Authorization' => 'Bearer '.$parent1->api_token]);
        $response->assertStatus(200);

        $ref_bonus = $response->original['success']['ref_bonuses'];

        if (!empty($ref_bonus)) {
            foreach($ref_bonus as $bonus) {
                $name = $bonus->name;

                $this->assertArrayHasKey($name, $refs);
                $this->assertEquals($refs[$name], $bonus->sum);
            }
        }
    }

    protected $vkData = [
        "profile" => "http://vk.com/123",
        "verified_email" => "1",
        "bdate" => "1.11.1987",
        "photo_big" => "https://sun9-61.userapi.com/impg/P1mFyz30kylqgWKrQ1dAXIPlsWYaRBvmBVyM-w/fB2B1EawNCk.jpg?size=200x0&quality=90&sign=150258c1025f1d9c1dbc370f0c1a7ca7",
        "nickname" => "Alba",
        "uid" => "123",
        "last_name" => "Иванов",
        "manual" => "nickname",
        "sex" => "2",
        "photo" => "https://sun9-61.userapi.com/impg/P1mFyz30kylqgWKrQ1dAXIPlsWYaRBvmBVyM-w/fB2B1EawNCk.jpg?size=200x0&quality=90&sign=150258c1025f1d9c1dbc370f0c1a7ca7",
        "identity" => "http://vk.com/123",
        "city" => "Санкт-Петербург",
        "country" => "Россия",
        "original_city" => "Санкт-Петербург",
        "network" => "vkontakte",
        "first_name" => "Иван",
        "email" => "noemail@mail.ru",
        'lang' => 'ru',
        'cur' => Transaction::CUR_RUB,
    ];

    public function testULoginbyVkDataWithRefCode()
    {
        $parent = User::factory()->create();

        $response = $this->post('api/login/ulogin/data', [
            'data'      => $this->vkData,
            'ref_code'  => $parent->ref_code,
        ]);

        $response->assertStatus(200);

        $user = User::where('email', 'noemail@mail.ru')->first();

        $this->assertEquals($parent->id, $user->parent_id);
    }

    protected function details(User $user): string
    {
        $response = $this->get('/api/user?api_token=' .
            $user->api_token);

        $orig = $response->original;

        return json_encode($orig, JSON_PRETTY_PRINT);
    }

    public function testParentHasCorrectRefBonuses()
    {
        $parent = User::create([
            'id'        => 201,
            'name'      => 'parent1',
            'password'  => bcrypt('secret'),
            'email'     => 'parent1@smm.example.com',
            'roles'     => [UserRole::ROLE_VERIFIED],
            'api_token' => User::getFreeToken(),
        ]);

        $res = $this->post('/api/register', [
            'login'             => 'rose',
            'email'             => 'rose@example.com',
            'password'          => 'secret',
            'password_confirm'  => 'secret',
            'ref_code'          => $parent->ref_code,
            'lang'              => 'ru',
            'cur'               =>  Transaction::CUR_RUB,
        ]);

        $res->assertStatus(200);

        $token = $res->original->getData()['token'];

        $this->assertNotNull($token);

        $user = User::where('name', 'rose')->firstOrFail();

        $this->assertEquals($parent->id, $user->parent_id);
    }
}