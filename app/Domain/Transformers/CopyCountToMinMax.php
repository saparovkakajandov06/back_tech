<?php

namespace App\Domain\Transformers;

use App\UserService;

class CopyCountToMinMax implements ITransformer
{
    public function transform(array $params, UserService $us): array
    {
        return array_merge($params, [
            'min' => $params['count'],
            'max' => $params['count'],
        ]);
    }
}
