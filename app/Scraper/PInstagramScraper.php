<?php

namespace App\Scraper;

use App\Proxy;

class PInstagramScraper implements InstagramScraper
{
    public function getMedia($link)
    {
        return Proxy::getRequest('http://p-scraper:8000/api/media', [
            'link' => $link,
        ], true);
    }

    public function getUser($login)
    {
        return Proxy::getRequest('http://p-scraper:8000/api/user', [
            'login' => $login,
        ], true);
    }

    public function getPosts($login, $count)
    {
        return Proxy::getRequest('http://p-scraper:8000/api/posts', [
            'login' => $login,
            'count' => $count,
        ], true);
    }

    public function healthCheck(): bool
    {
        return Proxy::available(10, 0.9) >= 3;
    }
}
