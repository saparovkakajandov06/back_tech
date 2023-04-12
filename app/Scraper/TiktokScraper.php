<?php

namespace App\Scraper;

interface TiktokScraper
{
    public function getUser($login);

    public function healthCheck(): bool;
}
