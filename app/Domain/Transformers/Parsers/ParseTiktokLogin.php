<?php

namespace App\Domain\Transformers\Parsers;

use App\Domain\Transformers\ITransformer;
use App\Exceptions\Reportable\ParserException;
use App\Parsers\TiktokLinkParser;
use App\UserService;

class ParseTiktokLogin implements ITransformer
{
    public function transform(array $params, UserService $us): array
    {
        $lp = new TiktokLinkParser();

        try {
            $parsedLogin = $lp->tiktokLogin($params['login']);
        } catch (\Throwable $e) {
            throw new ParserException(__('s.bad_tiktok_login'));
        }

        if (! $parsedLogin) {
            throw new ParserException(__('s.null_login'));
        }

        return array_merge($params, [
            'login' => $parsedLogin,
            'link' => "https://www.tiktok.com/@$parsedLogin",
        ]);
    }
}
