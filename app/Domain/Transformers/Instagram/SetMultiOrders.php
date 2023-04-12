<?php

namespace App\Domain\Transformers\Instagram;

use App\Domain\Transformers\ITransformer;
use App\Scraper\Models\IgMedia;
use App\UserService;

class SetMultiOrders implements ITransformer
{
    public function transform(array $input, UserService $us): array
    {
        // array without keys
        $input = array_diff_key($input, array_flip(['tag', 'api_token']));

        $posts = IgMedia::fromLogin($input['login'], $input['posts']);

        return array_map(
            fn($post) => array_merge([
                    'link' => $post->link,
                    'img' => $post->img
            ], $input),
        $posts);
    }
}
