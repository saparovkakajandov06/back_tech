<?php

namespace App\Scraper;

use App\Proxy;

class TTScraper implements TiktokScraper
{
    public function getUser($login)
    {
        return Proxy::getRequest('http://tt-scraper:8000/api/user', [
            'login' => $login,
        ]);
    }

    public function healthCheck(): bool
    {
        return Proxy::available(10, 0.9) >= 3;
    }
}
