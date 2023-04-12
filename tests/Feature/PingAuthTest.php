<?php

namespace Tests\Feature;

use App\Role\UserRole;
use App\Transaction;
use App\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\TF\TFHelpers;

class PingAuthTest extends TestCase
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

    public function testPing()
    {
        $this->get('/api/ping')
            ->assertStatus(200)
            ->assertJson(['message' => 'pong']);
    }

    public function testNotAuthorized()
    {
        $response = $this->get('/api/test_auth');
        $response->assertStatus(401);
        $response->assertJson([
            'status' => 'error',
            'error' => AuthenticationException::class,
            'message' => __('s.unauthenticated'),
        ]);
    }

    public function testAuthorizeWithTokenInHeader()
    {
        $response = $this->get('/api/test_auth', [
            'Authorization' => 'Bearer '.$this->user->api_token,
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'test auth']);
    }

    public function testAuthorizeWithTokenInQuery()
    {
        $response = $this->get('/api/test_auth?api_token='
            .$this->user->api_token);

        $response->assertStatus(200)
            ->assertJson(['message' => 'test auth']);
    }

    public function testAuthorizeWithTokenInPost()
    {
        $response = $this->post('/api/test_auth', [
            'api_token' => $this->user->api_token,
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'test auth']);
    }

    public function testModeratorCanAccessModeratorEndpoint()
    {
        $moderator = User::factory()->create([
            'roles' => [UserRole::ROLE_MODERATOR],
        ]);
        $this->assertTrue($moderator->hasRole(UserRole::ROLE_MODERATOR));

        $response = $this
            ->get('/api/test_moderator?api_token='.$moderator->api_token);
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'test moderator',
            ]);
    }

    public function testUserCanNotAccessModeratorEndpoint()
    {
        $moderator = User::factory()->create([
            'roles' => [],
        ]);
        $this->assertFalse($moderator->hasRole(UserRole::ROLE_MODERATOR));

        $response = $this
            ->get('/api/test_moderator?api_token='.$moderator->api_token);
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'error',
                'message' => __('s.unauth'),
            ]);
    }

    public function testUserCanAccessPublicEndpoint()
    {
        $user = User::factory()->create([
            'roles' => [],
        ]);
        $this->assertFalse($user->hasRole(UserRole::ROLE_MODERATOR));

        $response = $this
            ->get('/api/ping?api_token='.$user->api_token);
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'pong',
            ]);
    }

    public function testModeratorCanNotAccessAdminEndpoint()
    {
        $moderator = User::factory()->create([
            'roles' => [],
        ]);
        $this->assertFalse($moderator->hasRole(UserRole::ROLE_MODERATOR));

        $response = $this
            ->get('/api/test_admin?api_token='.$moderator->api_token);
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'error',
                'message' => __('s.unauth'),
            ]);
    }

    public function testAdminCanAccessAdminEndpoint()
    {
        $admin = User::factory()->create([
            'roles' => [UserRole::ROLE_ADMIN],
        ]);
        $this->assertTrue($admin->hasRole(UserRole::ROLE_ADMIN));

        $response = $this
            ->get('/api/test_admin?api_token='.$admin->api_token);
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'test admin',
            ]);
    }
}
