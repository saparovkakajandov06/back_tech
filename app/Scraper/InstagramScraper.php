<?php

namespace App\Scraper;

interface InstagramScraper
{
    public function getMedia($link);

    public function getPosts($login, $count);

    public function getUser($login);

    public function healthCheck(): bool;
}
