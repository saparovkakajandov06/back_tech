<?php

namespace App\Domain\Transformers\Instagram;

use App\Domain\Transformers\ITransformer;
use App\Exceptions\Reportable\ReportableException;
use App\UserService;

class SetManyOrdersFromIgScraperData implements ITransformer
{
    public function transform(array $input, UserService $us): array
    {
        // array without keys
        $input = array_diff_key($input, array_flip(['tag', 'api_token']));

        $scrapedPostsArray = $input['sdata'] ??
            throw new ReportableException('Empty sdata');

        if (($c = count($scrapedPostsArray)) < $input['posts']) {
            throw new ReportableException("Not enough posts: $c < " . $input['posts']);
        }

        $createdOrders = array_map(
            function ($post) use ($input) {
                return array_merge($input, [
                    'link' => 'https://www.instagram.com/p/' . data_get($post, 'code'),
                    'sdata' => $post,
                ]);
            },
            $scrapedPostsArray);

        return $createdOrders;
    }
}
