<?php

namespace App;

use Illuminate\Support\Facades\Log;

/**
 * Class Util
 *
 * Различные вспомогательные методы
 *
 * @package App
 *
 */
class Util
{
    /**
     * Парсит логин из URL
     *
     * @param string $data
     *
     * @return string
     */
    public static function parseInstagramLogin($data)
    {
        if (str_contains($data, 'instagram.com')) {
            return self::getInstagramLoginFromUrl($data);
        } else {
            return $data;
        }
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public static function getInstagramLoginFromUrl($url): string
    {
        $parts = parse_url($url);
        $pathParts = explode('/', $parts['path']);
        $login = $pathParts[1];
        return $login;
    }
}
