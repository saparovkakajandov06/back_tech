<?php

namespace App\Parsers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class TiktokLinkParser
{

// https://m.tiktok.com/h5/share/usr/6936965173521695745.html?_d=dhbdi1i4103dek&language=ru&sec_uid=MS4wLjABAAAAjse-FFbDD6x25cLYx2hr5KsjZh_ES5CYTjUjHb8mwz0_EL6OAZtZf-zXZJNiVa-Z&share_author_id=6936965173521695745&u_code=dhcc7557dll6l9&sec_user_id=MS4wLjABAAAAjse-FFbDD6x25cLYx2hr5KsjZh_ES5CYTjUjHb8mwz0_EL6OAZtZf-zXZJNiVa-Z&utm_source=copy&utm_campaign=client_share&utm_medium=ios&tt_from=copy&user_id=6936965173521695745&share_link_id=58B46B4A-D4C2-4B2B-90E2-62392BD5A10B

    private function giveMeBody($input): ?string
    {
        $key = md5($input);
        if (!Cache::has($key)) {
            try {
                if (env('TIKTOK_WITH_COOKIES', false)) {
                    $body = Http::withCookies([
                        '_abck' => env('TIKTOK_COOKIE_ABCK', ''),
                        'MONITOR_WEB_ID' => env('TIKTOK_COOKIE_MONITOR_WEB_ID', '')
                    ], '.tiktok.com')->get($input)->body();
                } else {
                    $body = Http::get($input)->body();
                }
                $ttl = env('TIKTOK_BODY_TTL', 300);
                Cache::put($key, $body, $ttl);
            } catch (\Throwable $e) {
                return null;
            }
        }
        return Cache::get($key);

//        return Http::get($input)->body();
    }

    private function fetchTiktokLogin($input): ?string
    {
        try {
            $body = $this->giveMeBody($input);

            $re = '/"uniqueId":"([a-zA-Z0-9._]{2,24})",/m';
            preg_match($re, $body, $matches);
            [$total, $login] = $matches;
        } catch (\Throwable $e) {
            $login = null;
        }

        return $login;
    }

    private function fetchTiktokLink($input)
    {
        // https://www.tiktok.com/@r1chee/video/6915021189471538434\"
        try {
            $body = $this->giveMeBody($input);

            $re = '/"uniqueId":"([a-zA-Z0-9._]{2,24})",/m';
            preg_match($re, $body, $matches);
            [$total, $login] = $matches;

            $re = "/https:\/\/www.tiktok.com\/@$login\/video\/\d+/";
            preg_match($re, $body, $matches);
            [$link] = $matches;
        } catch (\Throwable $e) {
            $link = null;
        }

        return $link;
    }

    private function tiktokLoginFetchable($input): bool
    {
        // https://m.tiktok.com/h5/share/usr/6936965173521695745.html
        $a = intval(preg_match('/tiktok.com\/h5\/share\/usr/', $input));

        // https://vm.tiktok.com/ZSJ9r33ca/
        // https://vt.tiktok.com/U3TpuD/ // only login?
        $b = intval(preg_match('/https:\/\/(vm|vt).tiktok.com\/[a-zA-Z]+/', $input));

        // https://m.tiktok.com/v/6853144159734222085.html
        $c = intval(preg_match('/https:\/\/m.tiktok.com\/v\/\d+/', $input));

        // https://t.tiktok.com/i18n/share/video/6923236192695684353
        $d = intval(preg_match('/https:\/\/t.tiktok.com\/i18n\/share/', $input));

        return ($a + $b + $c + $d) > 0;
    }

    private function tiktokLinkFetchable($input): bool
    {
        // https://m.tiktok.com/h5/share/usr/6936965173521695745.html
        $a = intval(preg_match('/tiktok.com\/h5\/share\/usr/', $input));

        return $a > 0;
    }

    // другие форматы ссылок, скрейпер их понимает
    private function isValidLink($input): bool
    {
        // https://www.tiktok.com/t/ZSemEAtBh/
        $a = intval(preg_match('/(https:\/\/)?(www.)?tiktok.com\/t\/[a-zA-Z]+/', $input));

        // https://vm.tiktok.com/ZSJ9r33ca/
        // https://vt.tiktok.com/U3TpuD/ // only login?
        $b = intval(preg_match('/https:\/\/(vm|vt).tiktok.com\/[a-zA-Z]+/', $input));

        // https://m.tiktok.com/v/6853144159734222085.html
        $c = intval(preg_match('/https:\/\/m.tiktok.com\/v\/\d+/', $input));

        // https://t.tiktok.com/i18n/share/video/6923236192695684353
        $d = intval(preg_match('/https:\/\/t.tiktok.com\/i18n\/share/', $input));

        return ($a + $b + $c + $d) > 0;
    }

    public function tiktokLogin($input): ?string
    {
        // raw login
        if(! str_contains($input, 'tiktok.com')) {
            $pattern = '/@?([a-zA-Z0-9._]+)/';
            preg_match($pattern, $input, $matches);
            [$full, $login] = $matches;

        } else if ($this->tiktokLoginFetchable($input)) {

            $login = $this->fetchTiktokLogin($input);
        } else {
            // parse login

            $pattern = '/(https?:\/\/)?(www.)?tiktok.com\/@?([a-zA-Z0-9._]+)/';
            preg_match($pattern, $input, $matches);
            [ $full, $https, $www, $login ] = $matches;
        }

        return $login;
    }

    public function tiktokLink($input): ?string
    {
        if ($this->tiktokLinkFetchable($input)) {
            return $this->fetchTiktokLink($input);
        }
        else {
            // https://www.tiktok.com/@login/video/123
            $pattern = '/(https?:\/\/)?(www.)?tiktok.com\/@?([a-zA-Z0-9._]+)\/video\/(\d+)/';
            if (preg_match($pattern, $input, $matches) === 1) {
                [ $full, $https, $www, $login, $id] = $matches;
                return "https://www.tiktok.com/@$login/video/$id";
            }
            elseif ($this->isValidLink($input)) {
                return $input;
            }
            else {
                return null;
            }
        }
    }
}
