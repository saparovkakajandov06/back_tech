<?php

namespace App\VK;

use App\PremiumStatus;
use App\Services\VKTransport;

class User
{
    public $id;
    public $vk, $first_name, $last_name;

    public function __construct($id)
    {
        $this->id = $id;
        $this->vk = app(VKTransport::class);
    }

    public function getFriends()
    {
        $methodName = "friends.get";
        $params = ["user_id" => $this->id];

        $res = $this->vk->get($methodName, $params);

//        print_r($res->response->count);

        $friends = [];

        foreach ($res->response->items as $id) {
            $friends[] = new User($id);
        }

        return $friends;
    }

    public function loadNames()
    {
        $methodName = "users.get";
        $params = ['user_id' => $this->id];
        $res = $this->vk->get($methodName, $params);

//        dd($res);

        $this->first_name = $res->response[0]->first_name;
        $this->last_name = $res->response[0]->last_name;
    }

    public function __toString()
    {
        return "VK User id: $this->id $this->first_name $this->last_name\n";
    }
}
