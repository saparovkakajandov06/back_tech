<?php

namespace App\Domain\Transformers\Instagram;

use App\Domain\Transformers\ITransformer;
use App\Scraper\Models\IgUser;
use App\UserService;

class SetImgFromLogin implements ITransformer
{
    public function transform(array $params, UserService $us): array
    {
        return array_merge($params, [
            'img' => IgUser::fromLogin($params['login'])->profilePhoto,
        ]);
    }
}
