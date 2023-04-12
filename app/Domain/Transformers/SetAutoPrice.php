<?php

namespace App\Domain\Transformers;

use App\UserService;

class SetAutoPrice implements ITransformer
{
    public function transform(array $input, UserService $us): array
    {
        return array_map(
            fn($params) => array_merge($params, [
                'cost' => $us->getFinalCost($params['posts'] * avg($params['min'], $params['max']), $params['cur']),
            ]),
            $input
        );
    }
}
