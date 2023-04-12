<?php

namespace Tests\Feature;

use App\Responses\ApiResponse;
use App\Role\UserRole;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\TF\TFHelpers;

class AdminUsersTest extends TestCase
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
    }

    public function testSearchByName()
    {
        $name = $this->user->name;
        $res = $this->withToken($this->admin->api_token)
            ->get("/api/admin/users?name=$name")
            ->json();

        $this->assertEquals($this->user->name, $res['data'][0]['name']);
        $this->assertEquals($this->user->email, $res['data'][0]['email']);

        collect([
            'balance_rub',
            'balance_usd',
            'sum_balance_rub',
            'sum_balance_usd'
        ])->each(fn($field) => $this->assertArrayHasKey($field, $res['data'][0]));
    }

    public function testUserNotFoundByName()
    {
        $response = $this->withToken($this->admin->api_token)
            ->get('api/admin/users?name=not_existed_user')
            ->json();

        $this->assertEquals(User::notFound, $response['message']);
    }

    public function testSearchByEmail()
    {
        $response = $this->withToken($this->admin->api_token)
            ->get("api/admin/users?email={$this->user->email}")
            ->json();

        $firstResult = $response['data'][0];

        $this->assertEquals($this->user->name, $firstResult['name']);
        $this->assertEquals($this->user->email, $firstResult['email']);
        $this->assertArrayHasKey('balance_rub', $firstResult);
        $this->assertArrayHasKey('sum_balance_rub', $firstResult);
    }

    public function testUserNotFoundByEmail()
    {
        $response = $this->withToken($this->admin->api_token)
            ->get('api/admin/users?email=not_existed_user@gmail.com')
            ->json();

        $this->assertEquals(User::notFound, $response['message']);
    }

    public function testUserTransations()
    {
        $this->withToken($this->admin->api_token)
            ->get('/api/admin/user/' . $this->user->id . '/transactions')
            ->assertStatus(200)
            ->assertJson(['status' => 'success']);
    }

    public function testGetReferalsByUserId()
    {
        $this->withToken($this->admin->api_token)
            ->get('/api/admin/user/' . $this->user->id . '/refs')
            ->assertStatus(200)
            ->assertJson(['status' => 'success']);
    }
}
