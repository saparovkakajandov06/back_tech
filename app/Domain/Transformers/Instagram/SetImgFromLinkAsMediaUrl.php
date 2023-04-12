<?php

namespace App\Domain\Transformers\Instagram;

use App\Domain\Transformers\ITransformer;
use App\Scraper\Models\IgMedia;
use App\UserService;

class SetImgFromLinkAsMediaUrl implements ITransformer
{
    public function transform(array $params, UserService $us): array
    {
        return array_merge($params, [
            'img' => IgMedia::fromUrl($params['link'])->img,
        ]);
    }
}
