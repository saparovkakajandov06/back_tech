<?php

namespace App\Domain\Transformers\Instagram;

use App\Domain\Transformers\ITransformer;
use App\UserService;
use App\Util;

class CreateLinkFromLogin implements ITransformer
{
    public function transform(array $params, UserService $us): array
    {
        $login = Util::parseInstagramLogin($params['login']);
        $link = 'https://www.instagram.com/'.$login;

        return array_merge($params, ['link' => $link]);
    }
}
