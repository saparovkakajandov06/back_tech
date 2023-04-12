<?php

namespace App\Scraper\Simple;

interface ISimpleIgScraper
{
    public function profile($login);

    public function media($url);

    public function feed($login, $posts);

    const IMAGE = 'image';
    const VIDEO = 'video';
    const SIDECAR = 'sidecar';
}
