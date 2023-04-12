<?php

namespace App\Domain\Transformers\Parsers;

use App\Domain\Transformers\ITransformer;
use App\Exceptions\Reportable\ParserException;
use App\Parsers\IgLinkParser;
use App\UserService;

class ParseInstagramLogin implements ITransformer
{
    public function transform(array $params, UserService $us): array
    {
        $lp = new IgLinkParser();
        try {
            $parsedLogin = $lp->login($params['login']);
        }
        catch (\Throwable $e) {
            throw new ParserException(__('s.bad_instagram_login'));
        }

        if (! $parsedLogin) {
            throw new ParserException(__('s.null_login'));
        }

        return array_merge($params, [
            'login' => $parsedLogin,
            'link' => 'https://www.instagram.com/' . $parsedLogin,
        ]);
    }
}
