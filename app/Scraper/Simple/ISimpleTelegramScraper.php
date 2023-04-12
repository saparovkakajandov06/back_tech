<?php

namespace App\Scraper\Simple;

interface ISimpleTelegramScraper
{
    public function profile($login);

    public function views($login, $postId);
}
