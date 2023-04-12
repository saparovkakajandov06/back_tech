<?php

namespace App\Domain\Transformers;

use App\UserService;

class CopyLoginToLink implements ITransformer
{
    public function transform(array $params, UserService $us): array
    {
        return array_merge($params, [
            'link' => $params['login'],
        ]);
    }
}
