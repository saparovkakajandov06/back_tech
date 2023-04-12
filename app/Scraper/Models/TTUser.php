<?php

namespace App\Scraper\Models;

use App\Scraper\TiktokScraper;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TTUser
{
//"nickname": "Ольга Бузова",
//"avatarMedium": "https://p16-sign-sg.tiktokcdn.com/musically-maliva-obj/1643837999171589~c5_720x720.jpeg?x-expires=1609977600&x-signature=Au%2FyFemYBVStB2iabTl4ZhEyy%2BM%3D",
//"followersCount": 6700000

    public $nickname;
    public $avatarMedium;
    public $followersCount;
    public $error;
    public $debug;

    private function __construct($data)
    {
        Log::info('data='.json_encode($data,128));

        $this->debug = $data['debug'];

        if ($this->error = $data['error']) return;

        try {
            $this->nickname = $data['nickname'];
            $this->avatarMedium = $data['avatarMedium'];
            $this->followersCount = $data['followersCount'];
        } catch (\Exception $e) {
            $this->error = 'constructor exception';
        }
    }

    public static function fromLogin($login)
    {
        $key = __CLASS__ . __FUNCTION__ . $login;
        $ttl = env('SCRAPER_MODEL_TTL', 24);
        $data = Cache::remember($key, $ttl, function () use ($login) {
            return resolve(TiktokScraper::class)->getUser($login);
        });

        return new self($data);
    }
}