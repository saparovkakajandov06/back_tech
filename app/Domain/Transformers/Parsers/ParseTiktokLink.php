<?php

namespace App\Domain\Transformers\Parsers;

use App\Domain\Transformers\ITransformer;
use App\Exceptions\Reportable\ParserException;
use App\Parsers\TiktokLinkParser;
use App\UserService;

class ParseTiktokLink implements ITransformer
{
    public function transform(array $params, UserService $us): array
    {
        $lp = new TiktokLinkParser();
        try {
            $parsedLink = $lp->tiktokLink($params['link']);
        } catch (\Throwable $e) {
            throw new ParserException(__('s.bad_tiktok_link'));
        }

        if (! $parsedLink) {
            throw new ParserException(__('s.null_link'));
        }

        // https://www.tiktok.com/@tiktok/video/6800111723257941253
        return array_merge($params, [
            'link' => $parsedLink,
        ]);
    }
}
