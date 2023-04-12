<?php

namespace App\Scraper;

use App\Proxy;

class DYoutubeScraper implements YoutubeScraper
{
    public function getVideo($link)
    {
        return Proxy::getRequest('http://scraper:5000/api/youtube/video', [
            'link' => $link,
        ]);
    }

    public function healthCheck(): bool
    {
        return Proxy::available(10, 0.9) >= 3;
    }
}
