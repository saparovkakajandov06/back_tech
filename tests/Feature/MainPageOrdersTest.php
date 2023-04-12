<?php

namespace Tests\Feature;

use App\Role\UserRole;
use App\Transaction;
use App\User;
use App\UserService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tests\TF\TFHelpers;

class MainPageOrdersTest extends TestCase
{
    use DatabaseMigrations;

    public $admin;
    public $user;

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
    }

    protected function createLikesFromMain($autoToken = null)
    {
        $data = [
            'cancel_url'  => 'http://google.com',
            'count'       => 50,
            'description' => 'Test order from main',
            'link'        => 'https://www.instagram.com/p/CHYGjl3D06R/',
            'success_url' => 'http://google.com',
            'tag'         => UserService::INSTAGRAM_LIKES_MAIN,
        ];

        if ($autoToken) {
            $data['auto_token'] = $autoToken;
        }

        return $this->post('/api/c_orders/main', $data);
    }

    public function testOrderFromMainShouldReturnAutoToken()
    {
        $res = $this->createLikesFromMain()
                    ->assertStatus(Response::HTTP_OK)
                    ->assertJson([
                        'status'  => 'success',
                        'message' => 'ok, payment expected'
                    ]);

        $this->assertMatchesRegularExpression(
            '/[a-zA-Z\d]{60}/',
            $res->json('data.auto_token'));
    }

    public function testOrderWithAutoTokenShouldReturnThisAutoToken()
    {
        $t1 = $this->createLikesFromMain()->json('data.auto_token');
        $t2 = $this->createLikesFromMain($t1)->json('data.auto_token');
        $this->assertEquals(
            $t1, $t2,
            "1st auto-token '${t1}' is not equal to 2nd auto-token '${t2}'"
        );
    }

    public function testAutoUserShouldHaveThreeOrders()
    {
        $token = $this->createLikesFromMain()->json('data.auto_token');
        $this->createLikesFromMain($token);
        $this->createLikesFromMain($token);

        $user = User::where('api_token', $token)->firstOrFail();
        $this->assertCount(3, $user->compositeOrders);
    }

    public function testIfBadTokenShouldCreateNewUser()
    {
        $token = $this->createLikesFromMain('bad_token')
                      ->json('data.auto_token');

        $this->assertNotEquals('bad_token', $token);

        $user = User::whereApiToken($token)->firstOrFail();
        $this->assertCount(1, $user->compositeOrders);
    }
}