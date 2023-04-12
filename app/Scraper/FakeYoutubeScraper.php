<?php /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */

namespace App\Scraper;

class FakeYoutubeScraper implements YoutubeScraper
{
    public function getVideo($link)
    {
        return [
            'img' => 'http://fake',
            'error' => '',
        ];
    }

    public function healthCheck(): bool
    {
        return true;
    }
}
