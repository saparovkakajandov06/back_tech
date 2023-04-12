<?php

namespace App\Domain\Transformers;

use App\UserService;

class SetNoCost implements ITransformer
{
    public function transform(array $params, UserService $us): array
    {
        return array_merge($params, ['cost' => 0]);
    }
}
