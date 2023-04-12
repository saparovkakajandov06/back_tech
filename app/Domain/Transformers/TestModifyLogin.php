<?php

namespace App\Domain\Transformers;

use App\UserService;

class TestModifyLogin implements ITransformer
{
    public function transform(array $params, UserService $us): array
    {
        return collect($params)
            ->merge(['login' => $params['login'].'!'])
            ->all();
    }
}
