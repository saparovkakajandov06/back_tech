<?php

namespace App\Domain\Transformers\Parsers;

use App\Domain\Transformers\ITransformer;
use App\Exceptions\Reportable\ParserException;
use App\Parsers\TelegramLinkParser;
use App\UserService;

class ParseTelegramLogin implements ITransformer
{
    public function transform(array $params, UserService $us): array
    {
        $lp = new TelegramLinkParser();
        try {
            $parsedLogin = $lp->login($params['link']);
        } catch (\Throwable $e) {
            throw new ParserException(__('s.bad_telegram_link'));
        }

        if (! $parsedLogin) {
            throw new ParserException(__('s.null_link'));
        }

        return array_merge($params, [
            'login' => $parsedLogin,
            'link' => 't.me/' . $parsedLogin,
        ]);
    }
}
