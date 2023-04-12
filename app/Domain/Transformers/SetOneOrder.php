<?php

namespace App\Domain\Transformers;

use App\UserService;

class SetOneOrder implements ITransformer
{
    public function transform(array $params, UserService $us): array
    {
        $data = [
            collect($params)->except(['tag', 'api_token'])->all(),
        ];

        return $data;
    }
}
