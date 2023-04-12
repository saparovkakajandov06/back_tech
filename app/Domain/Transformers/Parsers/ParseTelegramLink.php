<?php

namespace App\Domain\Transformers\Parsers;

use App\Domain\Transformers\ITransformer;
use App\Exceptions\Reportable\ParserException;
use App\Parsers\TelegramLinkParser;
use App\UserService;

class ParseTelegramLink implements ITransformer
{
    public function transform(array $params, UserService $us): array
    {
        $lp = new TelegramLinkParser();
        try {
            $parsedLink = $lp->link($params['link']);
        } catch (\Throwable $e) {
            throw new ParserException(__('s.bad_telegram_link'));
        }

        if (! $parsedLink) {
            throw new ParserException(__('s.null_link'));
        }

        try{
            $loginAndPostId = explode('/', $parsedLink);
            $login = $loginAndPostId[0];
            $postId = $loginAndPostId[1];
        } catch (\Throwable $e) {
            throw new ParserException(__('s.parser_error'));
        }

        return array_merge($params, [
            'link' => 't.me/' . $parsedLink,
            'login' => $login,
            'postId' => $postId
        ]);
    }
}
