<?php

namespace App\Domain\Transformers\Instagram;

use App\Domain\Transformers\ITransformer;
use App\Scraper\Models\IgMedia;
use App\UserService;

class SetLoginFromLinkAsMediaUrl implements ITransformer
{
    public function transform(array $params, UserService $us): array
    {
        if (isset($params['login'])) {
            return $params;
        }

        return array_merge($params, [
            'login' => IgMedia::fromUrl($params['link'])->profileName,
        ]);
    }
}
