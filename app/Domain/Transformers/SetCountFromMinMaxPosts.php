<?php

namespace App\Domain\Transformers;

use App\UserService;

class SetCountFromMinMaxPosts implements ITransformer
{
    public function transform(array $params, UserService $us): array
    {
        $count = $params['posts'] * avg($params['min'], $params['max']);
        return array_merge($params, ['count' => $count]);
    }
}
