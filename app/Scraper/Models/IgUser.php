<?php

namespace App\Scraper\Models;

use App\Scraper\InstagramScraper;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class IgUser
{

//'profilePhoto' => $user->getProfilePicUrl(),
//'login' => $request->link,
//'followersCount' => $user->getFollowsCount(),
//'error' => '',

    public $login;
    public $profilePhoto;
    public $followersCount;
    public $error;
    public $debug;

    private function __construct($data)
    {
        $this->debug = $data['debug'] ?? null;

        if ($this->error = $data['error'] ?? null)
            return;

        try {
            $this->login = $data['login'];
            $this->profilePhoto = $data['profilePhoto'];
            $this->followersCount = $data['followersCount'];

            $this->debug = $data['debug'] ?? null;
        } catch (\Exception $e) {
            Log::info("data="  .json_encode($data));
            Log::info('IgUser constructor exception ' . $e->getMessage());
            $this->error = 'constructor exception';
        }
    }

    public static function fromLogin($login, $scraper=null)
    {
        if (!$scraper) {
            $scraper = resolve(InstagramScraper::class);
        }

        $key = __CLASS__ . __FUNCTION__ . $login;
        $ttl = env('SCRAPER_MODEL_TTL', 24);
        $data = Cache::remember($key, $ttl, fn() => $scraper->getUser($login));

        return new self($data);
    }
}
