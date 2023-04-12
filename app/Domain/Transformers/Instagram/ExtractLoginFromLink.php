<?php

namespace App\Domain\Transformers\Instagram;

use App\Domain\Transformers\ITransformer;
use App\UserService;
use App\Util;

class ExtractLoginFromLink implements ITransformer
{
    /**
     * we need to make https://instagram.com/p|tv|reel/%hash%
     * from https://instagram.com/%username%/p|tv|reel/%hash%
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
        if (!$suffixIndex || 1 === $suffixIndex) {
            // seems like we got normal link without protocol (var 1)
            // or it doesn't contain nor /p/ nor /tv/ nor /reel/
            return $params;
        }
        $maybeDomains = $linkParts[$suffixIndex - 2];
        if (empty($maybeDomains)) {
            // seems like we got normal link with protocol (var 2)
            return $params;
        }
        $domains = explode('.', strtolower($maybeDomains));
        if (!(in_array('instagram', $domains) || in_array('instagr', $domains))) {
            // don't know what it is, but there's on login in this link or it's not from instagram
            return $params;
        }
        [ $login ] = array_splice($linkParts, $suffixIndex - 1, 1, []);
        return array_merge($params, [
            'login' => $login,
            'link' => implode('/', $linkParts)
        ]);
    }
}
