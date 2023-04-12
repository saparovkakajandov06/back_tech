<?php

namespace App\Domain\Transformers\Parsers;

use App\Domain\Transformers\ITransformer;
use App\Exceptions\Reportable\ParserException;
use App\Parsers\VkLinkParser;
use App\UserService;

class ParseVkLink implements ITransformer
{
    public function transform(array $params, UserService $us): array
    {
        $lp = new VkLinkParser();
        try {
            $parsedLink = $lp->link($params['link']);
        } catch (\Throwable $e) {
            throw new ParserException(__('s.bad_vk_link'));
        }

        if (! $parsedLink) {
            throw new ParserException(__('s.null_link'));
        }

        return array_merge($params, [
            'link' => $parsedLink,
        ]);
    }
}
