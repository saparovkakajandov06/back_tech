<?php

namespace App\Domain\Transformers\Instagram;

use App\Domain\Transformers\ITransformer;
use App\UserService;
use App\Util;

class SetLoginFromLink implements ITransformer
{
    public function transform(array $params, UserService $us): array
    {
        $login = Util::parseInstagramLogin($params['link']);

        return array_merge($params, ['login' => $login, 'title' => $login]);
    }
}
