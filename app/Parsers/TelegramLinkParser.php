<?php

namespace App\Parsers;

use Illuminate\Support\Str;

class TelegramLinkParser
{
    public function login($input)
    {
        if($this->isLink($input)){
            $login = $this->parseFromLink($input);
        } else {
            $login = $this->parseFromLogin($input);
        }
       
        return $login;
    }

    public function link($input)
    {
        if($this->isLink($input)){
            return $link = $this->parseLinkFromLink($input);
        } else {
            return null;
        }
    }

    private function isLink(string $input): bool
    {
        $res = Str::contains($input, "t.me/");
        
        return boolval($res);
    }

    private function parseFromLink(string $input): ?string
    {
        //min 5 symbols
        //max 32
        //correct symbols "a-z A-Z 0-9 _"
        //no "0-9_" in the start
        //no "_" in the end
        //"_" repeats no more than one time in succession

        $pattern = "/^(https?:\/\/)?(t\.me)\/([A-Za-z](?!.*(_)\\4{1})[A-Za-z0-9_]{4,31}(?<!_))$/";

        if (preg_match($pattern, $input, $matches) === 1) {
            [ $full, $https, $tme, $login] = $matches;
            
            return $login;
        } else {
            return null;
        }
    }

    private function parseFromLogin(string $input): ?string
    {
        $pattern = "/^(@)?([a-zA-Z](?!.*(_)\\3{1})[A-Za-z0-9_]{4,31}(?<!_))$/";

        if (preg_match($pattern, $input, $matches) === 1) {
            [$full, $sobaka, $login] = $matches;
            
            return $login;
        } else {
            return null;
        }
    }

    private function parseLinkFromLink($input)
    {
        $pattern = "/^(https?:\/\/)?(t\.me)\/([A-Za-z](?!.*(_)\\4{1})[A-Za-z0-9_]{4,31}(?<!_)\/([0-9]{1,20}))$/";

        if (preg_match($pattern, $input, $matches) === 1) {
            [ $full, $https, $tme, $login, $id] = $matches;
            
            return $login;
        } else {
            return null;
        }
    }
}
