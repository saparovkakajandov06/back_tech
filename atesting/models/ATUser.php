<?php

namespace App\Atesting\Models;

use GuzzleHttp\Client;

class ATUser
{
    public $id;

    public $email;

    public $name;
    public $login;

    public $password;
    public $token;

    public $refCode;
    public $parentId;

    public $premiumStatusId;

    public $lang;
    public $cur;
    public $balanceRUB;
    public $balanceUSD;

    const pingURL = 'http://localhost:8888/api/ping';
    const registerURL = 'http://localhost:8888/api/register';
    const detailsURL = 'http://localhost:8888/api/user';


    public function __construct($data)
    {
        $this->setData($data);
    }

    public function setData($data)
    {
        $g = make_data_getter($data);

        $this->id         = $g('id', $this->id);
        $this->email      = $g('email', $this->email);
        $this->name       = $g('name', $this->name);
        $this->login      = $g('login', $this->login);
        $this->password   = $g('password', $this->password);
        $this->token      = $g('token', $this->token);
        $this->refCode    = $g('ref_code', $this->refCode);
        $this->parentId   = $g('parent_id', $this->parentId);
        $this->premiumStatusId =
                $g('premium_status_id', $this->premiumStatusId);
        $this->lang       = $g('lang', $this->lang);
        $this->cur        = $g('cur', $this->cur);
        $this->balanceRUB = $g('balance_rub', $this->balanceRUB);
        $this->balanceUSD = $g('balance_usd', $this->balanceUSD);
    }

    public static function postJson($url, $params)
    {
        $client = new Client();
        $response = $client->post($url, [
          'json' => $params
        ]);
        $body = $response->getBody();

        return json_decode($body);
    }

    public static function getJson($url, $token='')
    {
        $client = new Client([
          'headers' => [
            'Authorization' => 'Bearer ' . $token,
          ]
        ]);

        $response = $client->get($url);
        $body = $response->getBody();

        return json_decode($body);
    }

    public static function register($email, $login, $pass): ATUser
    {
        $params = [
          'email' => $email,
          'login' => $login,
          'password' => $pass,
          'password_confirm' => $pass,
          'lang' => 'ru',
          'cur' => 'RUB',
        ];

        $json = self::postJson(self::registerURL, $params);

        $status = data_get($json, 'status');
        $token  = data_get($json, 'data.token');

        assert($status === "success", 'status !== success');
        assert(!empty($token), 'token is empty');

        $user = new ATUser([
            'token' => $token,
            'email' => $email,
            'login' => $login,
            'password' => $pass,
        ]);

        return $user;
    }

    public function loadDetails(): ATUser
    {
        $details = self::getJson(self::detailsURL, $this->token);
        $this->setData($details);

        return $this;
//        $mode = JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES;
//        echo json_encode($details, $mode);
    }
}
