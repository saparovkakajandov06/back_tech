<?php

namespace App\Domain\Transformers;

use App\UserService;

class SetNormalAutoPrice implements ITransformer
{
    public function transform(array $input, UserService $us): array
    {
        return array_map(
            fn($params) => array_merge($params, [
                'cost' => $us->getFinalCost($params['posts'] * $params['count'], $params['cur']),
            ]),
            $input
        );
    }
}
