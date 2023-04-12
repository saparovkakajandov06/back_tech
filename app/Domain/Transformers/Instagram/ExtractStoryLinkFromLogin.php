<?php

namespace App\Domain\Transformers\Instagram;

use App\Domain\Transformers\ITransformer;
use App\Domain\Transformers\Parsers\ParseInstagramLogin;
use App\UserService;

class ExtractStoryLinkFromLogin implements ITransformer
{
    /**
     * we need to make https://instagram.com/%username%
     * from https://instagram.com/stories/%username%/%story_id%/
     * exception: https://www.instagram.com/stories/highlights/%story_id%/
     */
    public function transform(array $params, UserService $us): array
    {
        $linkParts = explode('/', $params['login']);
        // var 1 [ 0 => '%domians%', 1 => 'stories', 2 => '%username%', 3 => '%story_id%' ]
        // var 2 [ 0 => '%protocol%', 1 => '', 2 => '%domians%', 3 => 'stories', 4 => '%username%', 5 => '%story_id%' ]
        // var 3 [ 0 => '%domians%', 1 => 'stories', 2 => 'highlights', 3 => '%story_id%' ]
        // var 4 [ 0 => '%protocol%', 1 => '', 2 => '%domians%', 3 => 'stories', 4 => 'highlights', 5 => '%story_id%' ]
        $suffixIndex = array_search('stories', $linkParts, true);
        if ($suffixIndex) {
            // input is a story link
            $maybeUsername = $linkParts[$suffixIndex + 1];
            if ($maybeUsername === 'highlights') {
                // input is a highlighted story link (var 3-4)
                // it's ok for now
                return array_merge($params, [
                    'login' => null,
                    'link' => $params['login'],
                ]);
            }
        }
        return resolve(ParseInstagramLogin::class)->transform($params, $us);
    }
}
