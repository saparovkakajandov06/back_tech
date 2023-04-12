<?php

namespace Tests\TF;

use App\Exceptions\NonReportable\NonReportableException;
use App\Transaction;
use App\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Testing\TestResponse;
use PHPUnit\TextUI\XmlConfiguration\PHPUnit;
use Tests\TestCase;
use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;

class TFUser
{
    const E2E_ROOT = 'http://172.29.0.1:1313';

    public $id;
    public $name;
    public $email;
    public $password;
    public $token;
    public $refCode;
    public $parentId;
    public $premiumStatusId;
    public $lang;
    public $cur;
    public $balanceRUB;
    public $balanceUSD;

    private function __construct() {}

    public static function create(array $data): self
    {
        $user = new self();
        $user->name = $data['name'] ?? null;
        $user->email = $data['email'] ?? null;
        $user->password = $data['password'] ?? null;

        return $user;
    }

    public static function withRandomData()
    {
        $g = new Gena();

        return TFUser::create([
            'name' => $g->login(),
            'email' => $g->email(),
            'password' => $g->password()
        ]);
    }

    public static function makeFromDB(User $dbUser): self
    {
        $user = new self();
        $user->token = $dbUser->api_token;
        $user->_fetchDetails();

        return $user;
    }

    public static function makeRandom(): self
    {
        return self::withRandomData()->register()->login();
    }

    public function makeRandomRef(): self
    {
        return self::withRandomData()
            ->register([ 'ref_code' => $this->refCode ])
            ->login();
    }

    public function login(): self
    {
        if ($this->name) {
            $response = Http::post(self::E2E_ROOT . '/api/login/local', [
                'login' => $this->name,
                'password' => $this->password,
            ])->json();
        } elseif ($this->email) {
            $response = Http::post(self::E2E_ROOT . '/api/login/local', [
                'email' => $this->email,
                'password' => $this->password,
            ])->json();
        } else {
            throw new NonReportableException('No name or email');
        }

        assertArrayHasKey('token', $response, json_encode($response));
        $token = $response['token'];
        assertNotNull($token);

        $this->token = $token;

        $this->_fetchDetails();

        return $this;
    }

    public function register(array $data = []): self
    {
        $default = [
            'login' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'password_confirm' => $this->password,
            'lang' => User::LANG_RU,
            'cur' => Transaction::CUR_RUB,
        ];

        $data = array_merge($default, $data);

        $res = TFHttp::post('api/register', $data)
            ->assertStatus(200)
            ->assertStatusSuccess();
        TestCase::assertNotNull(data_get($res->json(), 'data.token'));

        return $this;
    }

    public function get(string $url, array $query = []): TFResponse
    {
        $headers = ['Authorization' => "Bearer $this->token"];

        return TFHttp::get($url, $query, $headers)
                ->assertStatus(200);
    }

    public function post(string $url, array $data = []): TFResponse
    {
        $headers = ['Authorization' => "Bearer $this->token"];

        return TFHttp::post($url, $data, $headers)
                ->assertStatus(200);
    }

    public function logout(): self
    {
        $this->post('/api/logout')
             ->assertStatus(200)
             ->assertJson([
                "message" => "Successfully logged out"
             ]);

        return $this;
    }

    private function _fetchDetails()
    {
        $res = Http::withToken($this->token)
            ->get(self::E2E_ROOT . '/api/user')->json();

        assertArrayHasKey('success', $res);

        $s = $res['success'];
        assertNotNull($s);

        $this->id = $s['id'];
        $this->name = $s['name'];
        $this->email = $s['email'];
        $this->refCode = $s['ref_code'];
        $this->parentId = $s['parent_id'];
        $this->premiumStatusId = $s['premium_status_id'];
        $this->lang = $s['lang'];
        $this->cur = $s['cur'];
        $this->balanceRUB = $s['balance_rub'];
        $this->balanceUSD = $s['balance_usd'];
    }

    public function __toString()
    {
        return "user id {$this->id} {$this->name} token {$this->token}";
    }

    public function createOrder(array $params): TFResponse
    {
        $default = [
            'link'              => 'http://link_to',
            'count'             => '0',
            'tag'               => 'SOME_BAD_TAG',
            'api_token'         => $this->token,
            'region_value'      => 'CIS',
            'force_cur'         => $this->cur,
            'country_value'     => 'RU',
        ];

        return TFHttp::post(
            '/api/c_orders',
            array_merge($default, $params)
        )->assertStatus(200)
         ->assertStatusSuccess();
    }
}
