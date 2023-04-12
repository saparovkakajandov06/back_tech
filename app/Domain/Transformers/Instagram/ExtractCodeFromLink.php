<?php

namespace App\Domain\Transformers\Instagram;

use App\Domain\Transformers\ITransformer;
use App\UserService;

class ExtractCodeFromLink implements ITransformer
{
    /**
     * we need to get %hash% from https://instagram.com/p|tv|reel/%hash%/
     * or https://instagram.com/%username%/p|tv|reel/%hash%/
     */
    public function transform(array $params, UserService $us): array
    {
        $linkParts = explode('/', $params['link']);
        // var 1 [ 0 => '%domians%', 1 => 'p|tv|reel', 2 => '%hash%' ]
        // var 2 [ 0 => '%protocol%', 1 => '', 2 => '%domians%', 3 => 'p|tv|reel', 4 => '%hash%' ]
        // var 3 [ 0 => '%domians%', 1 => '%username%', 2 => 'p|tv|reel', 3 => '%hash%' ]
        // var 4 [ 0 => '%protocol%', 1 => '', 2 => '%domians%', 3 => '%username%', 4 => 'p|tv|reel', 5 => '%hash%' ]
        $suffixIndex = either(
            array_search('p', $linkParts, true),
            array_search('tv', $linkParts, true),
            array_search('reel', $linkParts, true)
        );
        if (!$suffixIndex || count($linkParts) <= ($suffixIndex + 1)) {
            // seems like we got link without /p/ or /tv/ or /reel/
            // or link without %hash%
            return $params;
        }
        return array_merge($params, ['code' => $linkParts[$suffixIndex + 1]]);
    }
}
