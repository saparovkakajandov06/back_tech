<?php

namespace App\Domain\Transformers\Instagram;

use App\Domain\Transformers\ITransformer;
use App\Scraper\Models\IgMedia;
use App\Scraper\PInstagramScraper;
use App\UserService;

class RealSetMultiOrders implements ITransformer
{
    public function transform(array $input, UserService $us): array
    {
        $realScraper = new PInstagramScraper();

        // array without keys
        $input = array_diff_key($input, array_flip(['tag', 'api_token']));

        $posts = IgMedia::fromLogin(
            $input['login'], $input['posts'], $realScraper);

        return array_map(

            fn($post) => array_merge($input, [
                'link' => $post->link,
                'img' => $post->img
            ]),

            $posts);
    }
}
