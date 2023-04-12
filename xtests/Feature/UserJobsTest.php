<?php

namespace xtests\Feature;

use App\Action;
use App\Domain\Checkers\CheckUserDidLikeMedia;
use App\Domain\Models\Chunk;
use App\Domain\Models\Labels;
use App\Domain\Services\Local\ALocal;
use App\Domain\Splitters\DefaultSplitter;
use App\Domain\Transformers\Instagram\SetImgFromLinkAsMediaUrl;
use App\Domain\Transformers\Instagram\SetLoginFromLinkAsMediaUrl;
use App\Domain\Transformers\SetDefaultPriceFromCount;
use App\Domain\Transformers\SetOneOrder;
use App\Domain\Validators\CheckHasLinkAndCount;
use App\Domain\Validators\CheckLinkAsMediaUrl;
use App\Domain\Validators\CheckUserHasEnoughFunds;
use App\Domain\Validators\Local\CheckUserDidNotLikeMedia;
use App\Domain\Validators\Local\CheckUserHasInstagramLogin;
use App\Http\Controllers\UserJobsController;
use App\Transaction;
use App\User;
use App\UserService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use xtests\TestCase;

//use Illuminate\Foundation\Testing\WithoutMiddleware;

class UserJobsTest extends TestCase
{
    use DatabaseTransactions;

    const serviceName = UserService::IG_LIKES_TEST;
    const mediaUrls = [
        'http://first', 'http://second', 'http://third', 'http://fourth'
    ];

    protected User $user;
    protected User $customer;
    protected User $worker;

    public function setUp(): void
    {
        parent::setUp();
        
        $this->user     = User::factory()->create();
        $this->customer = User::factory()->create();
        $this->worker   = User::factory()->create();
    }

    private function createService()
    {
        UserService::create([
            'title' => 'Локальный тестовый сервис лайков',
            'tag' => self::serviceName,
            'labels' => [
                Labels::TYPE_LIKES,
                Labels::DISCOUNT_LIKES,
                // more config
            ],
            'pipeline' => [
                CheckHasLinkAndCount::class,
                CheckLinkAsMediaUrl::class,
                SetImgFromLinkAsMediaUrl::class,
                SetLoginFromLinkAsMediaUrl::class,

                SetOneOrder::class,
                SetDefaultPriceFromCount::class,
                CheckUserHasEnoughFunds::class,
            ],
            'splitter' => DefaultSplitter::class,
            'config' => [
                ALocal::class => [
                    'order' => 1,
                    'min' => 1,
                    'max' => 10,
                ],
            ],
            'price_list' => [
                1 => 0.19,
                1000 => 0.185,
                5000 => 0.182,
                10000 => 0.179,
                25000 => 0.178,
                50000 => 0.175,
                100000 => 0.175,
            ],
            'img' => '/svg/like.svg',
            'local_validation' => [
                CheckUserHasInstagramLogin::class,
                CheckUserDidNotLikeMedia::class,
            ],
            'local_checker' => CheckUserDidLikeMedia::class,
        ]);
    }

    private function createOrder(User $user)
    {
        $response = $this->post(route('create_order'), [
            'tag' => self::serviceName,
            'link' => collect(self::mediaUrls)->random(),
            'count' => rand(1, 3),
        ], auth_header($user));

        $response->assertStatus(200)
                 ->assertJson(['status' => 'success']);

        return $response;
    }

    public function testShouldHaveEmptyUserJobsList()
    {
        $response = $this->get(route('list_jobs'), auth_header($this->user));

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                    'message' => UserJobsController::indexMessage,
                    'data' => [],
                ]);
    }

    public function testShouldShowJob()
    {
        $this->markTestSkipped();

        $this->assertEquals(0, Chunk::count());

        // заказчик размещает заказ
        $this->createOrder($this->customer);

        $this->assertEquals(1, Chunk::count());
        $this->assertEquals(ALocal::class, Chunk::first()->service_class);

        // 1 задание видит рабочий
        $resWorker = $this->get(route('list_jobs'), auth_header($this->worker));
        $this->assertEquals(1, count($resWorker->original->getData()));

        // 1 задание видит заказчик
        $resCustomer = $this->get(route('list_jobs'), auth_header($this->customer));
        $this->assertEquals(1, count($resCustomer->original->getData()));
    }

    public function testChangeUser()
    {
        $this->markTestSkipped('user jobs test');

        $this->markTestSkipped();
        $res = $this->get('api/t?api_token=' . $this->user->api_token);
        $res->dump();
        Auth::setUser($this->customer);
        $res = $this->get('api/t?api_token=' . $this->customer->api_token);
        $res->dump();
        Auth::setUser($this->user);
        $res = $this->get('api/t?api_token=' . $this->user->api_token);
        $res->dump();
    }

    public function testUserCanTakeJob()
    {
        $this->markTestSkipped('user jobs test');

        // заказчик создает заказ
        $this->createOrder($this->customer);

        Auth::setUser($this->user);

        // юзер видит задание
        $res = $this->get(route('list_jobs'));
        $this->assertEquals(1, count($res->original->getData()));

        // юзер берет задание
        $chunk = Chunk::orderBy('id', 'desc')->limit(1)->first();
        $res = $this->post(route('take_job', ['id' => $chunk->id]));
        $res->assertStatus(200)
            ->assertJson(['status' => 'success']);

        // юзер не видит задание
        $res = $this->get(route('list_jobs'));
        $this->assertEquals(0, count($res->original->getData()));
    }

    public function testUserCanCompleteJob()
    {
        $this->markTestSkipped('user jobs test');

        // заказчик создает заказ
        $this->createOrder($this->customer);

        Auth::setUser($this->user);

        // юзер берет задание
        $this->post(route('take_job', ['id' => 1]))
                    ->assertStatus(200)
                    ->assertJson(['status' => 'success']);

        // Задание не выполнено и не оплачено
        $action = Action::latest()->take(1)->first();
        $this->assertFalse($action->completed);
        $this->assertFalse($action->paid);

        // юзер выполняет задание

        // юзер запрашивает проверку
        $this->user->update(['instagram_login' => 'ig_yes']);
        $this->post(route('check_job', ['actionId' => $action->id]));

        // Задание выполнено и оплачено
        $action->refresh();
        $this->assertTrue($action->completed);
        $this->assertTrue($action->paid);

        // юзер получает средства
        $t = Transaction::orderBy('id', 'desc')->limit(1)->first();
        $this->assertEquals(Transaction::INFLOW_USER_JOB, $t->type);
        $this->assertEquals(0.19, $t->amount);
        $this->assertEquals(0.19, $this->user->balance);
    }
}
