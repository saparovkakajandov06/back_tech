<?php

namespace App\Scraper\Models;

use App\Scraper\YoutubeScraper;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class YoutubeVideo
{
    public $img;
    public $error;
    public $debug;

    private function __construct($data)
    {
        $this->debug = $data['debug'];

        if ($this->error = $data['error']) return;

        try {
            $this->img = $data['img'];
        } catch (\Exception $e) {
            $this->error = 'constructor exception';
        }
    }

    public static function fromUrl($link)
    {
        $key = __CLASS__ . __FUNCTION__ . $link;
        $ttl = env('SCRAPER_MODEL_TTL', 24);
        $data = Cache::remember($key, $ttl, function() use ($link) {
            return resolve(YoutubeScraper::class)->getVideo($link);
        });

        Log::info("data=".json_encode($data));

        return new self($data);
    }
}
