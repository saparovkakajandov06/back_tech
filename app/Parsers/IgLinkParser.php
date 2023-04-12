<?php

namespace App\Parsers;


use Illuminate\Support\Str;

class IgLinkParser
{
    private function isLongLink($input): bool
    {
        $pattern = "/instagram/i";

        return preg_match($pattern, $input);
    }

    private function parseLoginFromLongLink($input): ?string
    {
        $ext = '.com|.org|.es|.it|.de|.ca|.hk|.ag|.pt|.cn';

        $pattern = "/(https?:\/\/)?(www.)?(?i)instagram(?-i)(.com)?($ext)?\/([a-zA-Z0-9._]+)/";

        if (preg_match($pattern, $input, $matches) === 1) {
            [ $full, $https, $www, $com, $x, $login] = $matches;
            return $login;
        } else {
            return null;
        }
    }

    private function parseLoginFromStoriesLongLink($input): ?string
    {
        $ext = '.com|.org|.es|.it|.de|.ca|.hk|.ag|.pt|.cn';

        $pattern = "/(https?:\/\/)?(www.)?(?i)instagram(?-i)(.com)?(.$ext)?\/stories\/([a-zA-Z0-9._]+)/";

        if (preg_match($pattern, $input, $matches) === 1) {
            [$full, $https, $www, $com, $x, $login] = $matches;
            return $login;
        } else {
            return null;
        }
    }

    private function isBadLogin($login): bool
    {
        return in_array($login, ['stories', 'reel', 'p', 'tv', 'highlights']);
    }

// The length must be between 3 and 30 characters.
// The accepted characters are like you said: a-z A-Z 0-9 dot(.) underline(_).
// It's not allowed to have two or more consecutive dots in a row.
// It's not allowed to start or end the username with a dot.

    private function isValidIgLogin($input): bool
    {
        $pattern = '/^[\w](?!.*?\.{2})[\w.]{1,28}[\w]$/';
        $res = preg_match($pattern, $input);
        return boolval($res);
    }

    public function login($input): ?string
    {
        if ($this->isLongLink($input)) {
            $login = $this->parseLoginFromLongLink($input);

            if ($login === 'stories') {
                $login = $this->parseLoginFromStoriesLongLink($input);
            }
            elseif ( $this->isBadLogin($login) ) {
                $login = null;
            }
        }
        else {
            $login = $input;
        }

        // remove @@@
        while (Str::startsWith($login, '@')) {
            $login = Str::substr($login, 1);
        }

        return $this->isValidIgLogin($login) ? $login : null;
    }

    public function noQuery($url): string
    {
        $url_parts = parse_url($url);

        $scheme = $url_parts['scheme'] ?? 'https';
        $host = $url_parts['host'];
        $path = $url_parts['path'] ?? '';

        return $scheme . '://' . $host . $path;
    }

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

        return $this->noQuery($input);
    }
}
