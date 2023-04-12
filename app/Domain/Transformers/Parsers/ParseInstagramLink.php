<?php

namespace App\Domain\Transformers\Parsers;

use App\Domain\Transformers\ITransformer;
use App\Exceptions\Reportable\ParserException;
use App\Parsers\IgLinkParser;
use App\UserService;

class ParseInstagramLink implements ITransformer
{
    public function transform(array $params, UserService $us): array
    {
        $lp = new IgLinkParser();
        try {
            $parsedLink = $lp->link($params['link']);
        } catch (\Throwable $e) {
            throw new ParserException(__('s.bad_instagram_link'));
        }

        if (! $parsedLink) {
            throw new ParserException(__('s.null_link'));
        }

        return array_merge($params, [
            'link' => $parsedLink,
        ]);
    }
}
