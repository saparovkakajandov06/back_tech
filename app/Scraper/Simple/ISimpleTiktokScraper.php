<?php

namespace App\Scraper\Simple;

interface ISimpleTiktokScraper
{
    public function profile($user);

    public function feed($user);

    public function video($url);
}