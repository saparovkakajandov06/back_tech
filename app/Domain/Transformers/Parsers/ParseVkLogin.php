<?php

namespace App\Domain\Transformers\Parsers;

use App\Domain\Transformers\ITransformer;
use App\Exceptions\Reportable\ParserException;
use App\Parsers\VkLinkParser;
use App\UserService;

class ParseVkLogin implements ITransformer
{
    public function transform(array $params, UserService $us): array
    {
        $lp = new VkLinkParser();
        try {
            $parsedLogin = $lp->login($params['login']);
        } catch (\Throwable $e) {
            throw new ParserException(__('s.bad_vk_link'));
        }

        if (! $parsedLogin) {
            throw new ParserException(__('s.null_link'));
        }

        return array_merge($params, [
            'login' => $parsedLogin,
            'link' => 'https://www.vk.com/' . $parsedLogin,
        ]);
    }
}
