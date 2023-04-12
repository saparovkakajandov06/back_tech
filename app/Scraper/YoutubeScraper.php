<?php

namespace App\Scraper;

interface YoutubeScraper
{
    public function getVideo($link);

    public function healthCheck(): bool;
}
