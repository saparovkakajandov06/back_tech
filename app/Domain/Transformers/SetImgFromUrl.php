<?php

namespace App\Domain\Transformers;

use App\Scraper\Models\YoutubeVideo;
use App\UserService;

/*
 * Youtube only
 */

class SetImgFromUrl implements ITransformer
{
    public function transform(array $params, UserService $us): array
    {
        return array_merge($params, [
//            'img' => $this->scraper->getYoutubePreview($params['link'])]
            'img' => YoutubeVideo::fromUrl($params['link'])->img,
        ]);
    }
}
