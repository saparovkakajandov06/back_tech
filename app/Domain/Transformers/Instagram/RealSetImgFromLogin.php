<?php

namespace App\Domain\Transformers\Instagram;

use App\Domain\Transformers\ITransformer;
use App\Scraper\Models\IgUser;
use App\Scraper\PInstagramScraper;
use App\UserService;

class RealSetImgFromLogin implements ITransformer
{
    public function transform(array $params, UserService $us): array
    {
        $realScraper = new PInstagramScraper();

        $igUser = IgUser::fromLogin($params['login'], $realScraper);

        return array_merge($params, [
            'img' => $igUser->profilePhoto
        ]);
    }
}
