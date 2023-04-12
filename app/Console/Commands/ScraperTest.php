<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use InstagramScraper\Instagram;

class ScraperTest extends Command
{
    protected $signature = 'st:scraper_test';
    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $ig = new Instagram();
        $medias = $ig->getMedias('yunia_star');

        foreach ($medias as $media) {
            $code = $media->getShortCode();
            $id = $media->getId();
            $comments = $media->getCommentsCount();
            $likes = $media->getLikesCount();

            echo "media $code has $comments comments and $likes likes\n";

            sleep(1);
        }
    }
}
