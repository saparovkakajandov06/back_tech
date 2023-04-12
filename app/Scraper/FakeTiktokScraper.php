<?php

namespace App\Scraper;

class FakeTiktokScraper implements TiktokScraper
{
    public function getUser($login)
    {
        // TODO: Implement getUser() method.
    }

    public function healthCheck(): bool
    {
        return true;
    }
}
