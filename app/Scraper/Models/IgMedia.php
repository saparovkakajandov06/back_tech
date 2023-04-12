<?php

namespace App\Scraper\Models;

use App\Scraper\InstagramScraper;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class IgMedia
{

//'img' => $media->getImageStandardResolutionUrl(),
//'profileName' => $owner->getUsername(),
//'profilePhoto' => $owner->getProfilePicUrl(),
//'likesCount' => $media->getLikesCount(),
//'viewsCount' => $media->getVideoViews(),
//'commentsCount' => $media->getCommentsCount(),
//'link' => $request->link,

    public $img;
    public $profileName;
    public $profilePhoto;
    public $likesCount;
    public $viewsCount;
    public $commentsCount;
    public $link;
    public $error;
    public $debug;


//[2021-03-28 01:26:51] local.INFO: IgMedia constructor exception Undefined index: likesCount
//[2021-03-28 01:26:51] local.INFO: {"img":"https:\/\/fake","profileName":"",
//"profilePhoto":"https:\/\/fake","countLike":0,"countViews":0,
//"countComment":0,"error":null,"status":"ok","link":"https:\/\/www.inst
//agram.com\/p\/CM8D6ySB-y4\/?igshid=16lpasqu5btd3"}

private function __construct($data)
    {
        $this->debug = $data['debug'] ?? null;

        if ($this->error = $data['error']) {
            Log::info('data[error]');
            return;
        }

        try {
            $this->img = $data['img'];
            $this->profileName = $data['profileName'];
            $this->profilePhoto = $data['profilePhoto'];
//            $this->likesCount = $data['likesCount'];
            $this->likesCount = $data['countLike'];
//            $this->viewsCount = $data['viewsCount'];
            $this->viewsCount = $data['countViews'];
//            $this->commentsCount = $data['commentsCount'];
            $this->commentsCount = $data['countComment'];
            $this->link = $data['link'];
        } catch (\Exception $e) {
            $this->error = 'constructor exception';
            Log::info('IgMedia constructor exception ' . $e->getMessage());
            Log::info(json_encode($data));
        }
    }

    public static function fromUrl($url, $scraper=null)
    {
        if (!$scraper) {
            $scraper = resolve(InstagramScraper::class);
        }

        $key = __CLASS__ . __FUNCTION__ . $url;
        $ttl = env('SCRAPER_MODEL_TTL', 24);
        $data = Cache::remember($key, $ttl, fn() => $scraper->getMedia($url));

        Log::info(json_encode($data));
        return new self($data);
    }

    public static function fromLogin($login, $count, $scraper=null)
    {
        if (!$scraper) {
            $scraper = resolve(InstagramScraper::class);
        }

        $key = __CLASS__ . __FUNCTION__ . $login . $count;
        $ttl = env('SCRAPER_MODEL_TTL', 24);
        $data = Cache::remember($key, $ttl, fn() => $scraper->getPosts($login, $count));

        Log::info('data='.json_encode($data, 128));

        if (! empty($data['error'])) {
            return [ 'debug' => $data['debug'] ?? '' ];
        } else {
            $debug = $data['debug'] ?? null;
            unset($data['debug']);

            return array_map(function($item) use ($debug) {
                $item['debug'] = $debug;
                return new self($item);
            }, $data);
        }
    }
}
