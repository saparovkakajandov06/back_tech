<?php

namespace Tests\Feature;

use App\Role\UserRole;
use App\Transaction;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\TF\TFHelpers;

class AuthTest extends TestCase
{
    use DatabaseMigrations;

    public $admin;
    public $user;
    public $pass;

    public function setUp(): void
    {
        parent::setUp();
//        TFHelpers::runCommonSeeders();
        TFHelpers::runTestSeeders();

        $this->pass = 'secret';

        $this->admin = User::factory()->create([
            'roles' => [UserRole::ROLE_ADMIN],
            'password' => bcrypt($this->pass),
        ]);

        $this->user = User::factory()->create([
            'password' => bcrypt($this->pass),
        ]);
    }

    public function testSimplePing()
    {
        $this->get('/api/ping')
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'pong',
                'data' => [],
            ]);
    }

    public function testLocalUserLogin()
    {
        $res = $this->post('/api/login/local', [
            'login' => $this->user->name,
            'password' => $this->pass,
        ]);

        $this->assertNotNull($res->json('data.token'));
    }

    public function loginsProvider()
    {
        return [
            [ 'test_reg_' . Str::random(6) ],
            [ 'test_reg_' . Str::random(8) ],
            [ 'test_reg_' . Str::random(12) ],
            [ 'test_reg_' . Str::random(3) . '.' . Str::random(3) ],
            [ Str::random(3) . '.' . Str::random(3) . '.' . Str::random(3) ],
            [ 'test_reg_...' . Str::random(8)],
        ];
    }

    /** @dataProvider loginsProvider */
    public function testWillRegisterAUser($login)
    {
        $password = '123456';

        $res = $this->post('api/register', [
            'login' => $login,
            'email' => Str::random(6) . '@smmtouch.store',
            'password' => $password,
            'password_confirm' => $password,
            'lang' => User::LANG_RU,
            'cur' => Transaction::CUR_RUB,
        ])->assertStatus(200);

        $this->assertNotNull($res->json('data.token'));
    }

    public function testRegistrationRequiresPasswordAndEmail()
    {
        $this->post('/api/register')
            ->assertJson([
                'message' => __('s.invalid_data'),
                'data' => [
                    'email' => ['The email field is required.'],
                    'password' => ['The password field is required.'],
                    "password_confirm" => ['The password confirm field is required.'],
                ],
            ]);
    }

    public function testRegistrationRequiresUniqueEmail()
    {
        $email = Str::random(6) . '@smmtouch.store';
        $password = '123456';

        User::create([
            'name' => 'SomeUniqueLogin',
            'email' => $email,
            'password' => bcrypt($password),
            'lang' => User::LANG_RU,
            'cur' => Transaction::CUR_RUB,
        ]);

        $user = [
          'login' => 'AnotherUniqueLogin',
          'email' => $email,
          'password' => $password,
          'password_confirm' => $password,
          'lang' => User::LANG_RU,
          'cur' => Transaction::CUR_RUB,
        ];

        $response = $this->post('api/register', $user);

        $response->assertStatus(200)->assertJson([
            "status" => "error",
            "error" => "App\\Exceptions\\Reportable\\EmailExistsException",
            "message" => "Email already exists",
        ]);
    }

    public function testRegistrationRequiresUniqueName()
    {
        $password = '123456';

        User::create([
          'name' => 'SomeCommonLogin',
          'email' => Str::random(12) . '@smmtouch.store',
          'password' => bcrypt($password),
          'lang' => User::LANG_RU,
          'cur' => Transaction::CUR_RUB,
        ]);

        $user = [
            'login' => 'SomeCommonLogin',
            'email' => Str::random(12) . '@smmtouch.store',
            'password' => $password,
            'password_confirm' => $password,
            'lang' => User::LANG_RU,
            'cur' => Transaction::CUR_RUB,
        ];

        $this->post('api/register', $user)->assertStatus(200)->assertJson([
            "status" => "error",
            "error" => "App\\Exceptions\\Reportable\\NameExistsException",
            "message" => "Login already exists",
        ]);
    }

    public function testLoginRequiresEmailAndPassword()
    {
        $this->post('/api/login/local', [])
            ->assertJson([
                'message' => __('s.invalid_data'),
                'data' => [
                    'password' => [__('s.password_required')],
                ],
            ]);
    }

    public function testShouldNotChangeToken()
    {
        $token1 = $this->post('/api/login/local', [
            'login' => $this->user->name,
            'password' => 'secret',
        ])->json('data.token');

        $token2 = $this->post('/api/login/local', [
            'login' => $this->user->name,
            'password' => 'secret',
        ])->json('data.token');

        $this->assertNotNull($token1);
        $this->assertEquals($token1, $token2);
    }

    public function testShouldChangeTokenAfter90Days()
    {
        $oldToken = $this->user->api_token;

        $this->user->token_updated_at =
            $this->user->token_updated_at->sub(91, 'days');
        $this->user->save();

        $newToken = $this->post('/api/login/local', [
            'login' => $this->user->name,
            'password' => $this->pass,
        ])->json('data.token');

        $this->assertNotNull($oldToken);
        $this->assertNotNull($newToken);
        $this->assertNotEquals($oldToken, $newToken);
    }

    public function testWillNotLogAnInvalidUserIn()
    {
        $this->post('/api/login/local', [
            'email' => 'test@email.com',
            'password' => 'notlegitpassword',
        ])
            ->assertStatus(200)
            ->assertJson([
                'status' => 'error',
                'message' => __('s.email_auth_error'),
            ]);
    }

    public function testRequirePasswordConfirmation()
    {
        $this->post('/api/register', [
            'email' => 'admin@admin.panel',
            'password' => 'secret',
            'password_confirm' => '123',
        ])
            ->assertStatus(200)
            ->assertJson([
                'status' => 'error',
                'message' => __('s.invalid_data'),
                'data' => [
                    'password_confirm' => [__('s.confirm_must_match')],
                ],
            ]);
    }

    public function testUserIsLoggedOutProperly()
    {
        $this->withToken($this->user->api_token)
            ->post('/api/logout')
            ->assertStatus(200)
            ->assertJson([
                "message" => "Successfully logged out"
            ]);

        $this->user->refresh();
        $this->assertEquals(null, $this->user->api_token);
    }

    public function testUserWithNullTokenShouldNotBeAbleToLogin()
    {
        $headers = [
            'Authorization' => "Bearer {$this->user->api_token}",
            'Accept' => 'application/json',
        ];

        $this->user->deleteToken();

        $this->get('/api/test_auth', $headers)
            ->assertStatus(401)
            ->assertJson([
                'status' => 'error',
                'message' => __('s.unauthenticated'),
            ]);
    }

    public function testRegisteredUserHasBasicPremiumStatus()
    {
        $name = 'abcabcuser';
        $email = 'the_email@example.com';
        $password = 'the_pass_1';

        $this->post('api/register', [
            'login' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirm' => $password,
            'lang' => User::LANG_RU,
            'cur' => Transaction::CUR_RUB,
        ]);

        $found = User::where('email', $email)->firstOrFail();

        $this->assertEquals('Базовый', $found->premiumStatus->name);
    }

    public function testRefHasPersonalPremiumStatus()
    {
        $name = 'abcabcuser';
        $email = 'the_email@example.com';
        $password = 'the_pass_1';

        $this->assertNotNull($this->user->ref_code);

        $this->post('api/register', [
            'login' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirm' => $password,
            'lang' => User::LANG_RU,
            'cur' => Transaction::CUR_RUB,

            'ref_code' => $this->user->ref_code,
        ]);

        $ref = User::where('name', $name)->firstOrFail();
        $this->assertEquals('Персональный', $ref->premiumStatus->name);
    }

    public function testInvalidRefHasBasicPremiumStatus()
    {
        $name = 'abcabcuser';
        $email = 'the_email@example.com';
        $password = 'the_pass_1';

        $this->post('api/register', [
            'login' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirm' => $password,
            'lang' => User::LANG_RU,
            'cur' => Transaction::CUR_RUB,

            'ref_code' => 'some_invalid_code',
        ]);

        $ref = User::where('name', $name)->firstOrFail();
        $this->assertEquals('Базовый', $ref->premiumStatus->name);
    }

    public function testLoginByNameWithoutEmail()
    {
        $name = 'login123';
        $password = 'secret123';

        User::create([
            'name' => $name,
            'password' => bcrypt($password),
        ]);

        $token = $this->post('/api/login/local', [
            'login' => $name,
            'password' => $password,
        ])->json('data.token');

        $this->assertNotNull($token);
    }

    public function testLoginByEmailWithoutName()
    {
        $email = 'email123@example.com';
        $password = 'secret123';

        User::create([
            'email' => $email,
            'password' => bcrypt($password),
        ]);

        $token = $this->post('/api/login/local', [
            'email' => $email,
            'password' => $password,
        ])->json('data.token');

        $this->assertNotNull($token);
    }

    protected $vkData = [
        "profile"           => "http://vk.com/123",
        "verified_email"    => "1",
        "bdate"             => "1.11.1987",
        "photo_big"         => "https://sun9-61.userapi.com/impg/P1mFyz30kylqgWKrQ1dAXIPlsWYaRBvmBVyM-w/fB2B1EawNCk.jpg?size=200x0&quality=90&sign=150258c1025f1d9c1dbc370f0c1a7ca7",
        "nickname"          => "Alba",
        "uid"               => "123",
        "last_name"         => "Иванов",
        "manual"            => "nickname",
        "sex"               => "2",
        "photo"             => "https://sun9-61.userapi.com/impg/P1mFyz30kylqgWKrQ1dAXIPlsWYaRBvmBVyM-w/fB2B1EawNCk.jpg?size=200x0&quality=90&sign=150258c1025f1d9c1dbc370f0c1a7ca7",
        "identity"          => "http://vk.com/123",
        "city"              => "Санкт-Петербург",
        "country"           => "Россия",
        "original_city"     => "Санкт-Петербург",
        "network"           => "vkontakte",
        "first_name"        => "Иван",
        "email"             => "noemail@mail.ru",
        "lang"              => User::LANG_RU,
        "cur"               => Transaction::CUR_RUB,
    ];

    public function testCreateUserByVkData()
    {
        $response = $this->post('api/login/ulogin/data', [
            'data' => $this->vkData,
        ])->assertStatus(200);

        $user = User::where('email', 'noemail@mail.ru')->first();
        $this->assertEquals($user->api_token, $response->json('data.token'));
    }

    public function testUserHasValidPasswordHash()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret'),
        ]);

        $this->assertTrue(Hash::check('secret', $user->password));
    }

    public function testChangeOldHash()
    {
        $dbUser = User::factory()->create([
            'name' => 'old_user',
            'password' => md5('old_secret'),
            'api_token' => 'the_token',
        ]);

        $this->post('/api/login/local', [
            'login' => 'old_user',
            'password' => 'old_secret',
        ]);

        $dbUser->refresh();
        $this->assertTrue(Hash::check('old_secret', $dbUser->password));
    }

    public function testCanLoginWithRandomToken()
    {
        $randomToken = Str::random(16);
        $this->admin->api_token = $randomToken;
        $this->admin->save();

        $this->withToken($randomToken)
            ->get('/api/test_admin')
            ->assertJson(['status' => 'success']);
    }

    public function testLogoutShouldClearToken()
    {
        $this->withToken($this->admin->api_token)
            ->post('/api/logout');

        $this->admin->refresh();
        $this->assertNull($this->admin->api_token);
    }

    public function testCannotLoginWithEmptyToken()
    {
        $token = $this->admin->api_token;

        $this->admin->update(['api_token' => null]);

        $this->withToken($token)
            ->get('/api/test_auth')
            ->assertJson(['status' => 'error']);
    }

//    public function testCanLoginAfterLogout()
//    {
//        $this->refreshApplication();
//    }
}
