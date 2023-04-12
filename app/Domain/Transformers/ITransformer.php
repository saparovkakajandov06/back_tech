<?php

namespace App\Domain\Transformers;

use App\UserService;

interface ITransformer
{
    public function transform(array $params, UserService $us): array;
}
