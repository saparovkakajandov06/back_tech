<?php

namespace App\Scraper\Simple;

interface ISimpleVkScraper
{
    public function profile($login);

    public function media($url);

    public function feed($login, $posts);

}
