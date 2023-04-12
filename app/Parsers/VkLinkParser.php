<?php

namespace App\Parsers;


class VkLinkParser
{
    private function isLongLink($input): bool
    {
        $pattern = "/vk/i";

        return preg_match($pattern, $input);
    }

    private function parseLoginFromLongLink($input): ?string
    {
        $pattern = "/(https?:\/\/)?(www.)?(m\.)?(?i)(vk.com|vkontakte.ru)(?-i)\/([a-zA-Z0-9._]+)/";

        if (preg_match($pattern, $input, $matches) === 1) {
            [ $full, $https, $mobile, $www, $com, $login] = $matches;
            return $login;
        } else {
            return null;
        }
    }

    private function isBadLogin($login): bool
    {
        return in_array($login, ['wall', 'photo']);
    }

// The length must be between 3 and 32 characters.
// The accepted characters are like you said: a-z A-Z 0-9 dot(.) underline(_).
// It's not allowed to have two or more consecutive dots in a row.
// It's not allowed to start or end the username with a dot.

    private function isValidIgLogin($input): bool
    {
        $pattern = '/^[\w](?!.*?\.{2})[\w.]{1,30}[\w]$/';
        $res = preg_match($pattern, $input);
        return boolval($res);
    }

    public function login($input): ?string
    {
        if ($this->isLongLink($input)) {
            $login = $this->parseLoginFromLongLink($input);

            if ( $this->isBadLogin($login) ) {
                $login = null;
            }
        } else {
            $login = $input;
        }
        return $this->isValidIgLogin($login) ? $login : null;
    }

    public function getShortLink($input)
    {
        $patternLong = "/(https?:\/\/)?(www.)?(m\.)?(?i)(vk.com|vkontakte.ru)(?-i).+\-?(wall|video|photo|story|club)(-?([\d]+)_(\d+))/";
        $patternShort = "/(https?:\/\/)?(www.)?(m\.)?(?i)(vk.com|vkontakte.ru)(?-i)\/([a-zA-Z0-9._]+)/";

        if (preg_match($patternLong, $input, $matches) === 1) {
            [ $full, $https, $www, $com, $login, $type, $id] = $matches;

            return $https . $www . $com  . $login . '/' . $type . $id;

        } elseif(preg_match($patternShort, $input, $matches) === 1) {
            [ $full ] = $matches;
            return $full;
        }
    }
//    public function noQuery($url): string
//    {
//        $url_parts = parse_url($url);
//
//        $scheme = $url_parts['scheme'] ?? 'https';
//        $host = $url_parts['host'];
//        $path = $url_parts['path'] ?? '';
//
//        return $scheme . '://' . $host . $path;
//        return $url;
//    }

    public function link($input): ?string
    {

//        if ( $this->isLongLink($input) ) {
//
//            $pattern = '/(https?:\/\/)?(www.)?instagram.com\/p\/([a-zA-Z0-9._]+)/';
//            if (preg_match($pattern, $input, $matches) === 1) {
//                [ $full, $https, $www, $code] = $matches;
//                return "https://www.instagram.com/p/$code";
//            } else {
//                return null;
//            }
//
//        } else {
//            return "https://www.instagram.com/p/$input";
//        }

        return $this->getShortLink($input);
    }
}
