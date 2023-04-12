<?php

namespace App\Scraper\Simple;

use App\Exceptions\Reportable\ScraperException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class KirtanTiktokScraper implements ISimpleTiktokScraper
{
// https://rapidapi.com/thekirtan/api/tiktok28

    private string $key;
    private string $host;
    private int $ttl;

    public function __construct()
    {
        $this->key = env('TIKTOK_SCRAPER_KEY');
        $this->host = env('TIKTOK_SCRAPER_HOST');
        $this->ttl = env('TIKTOK_SCRAPER_TTL');
    }

    private function headers() {
        return [
            "x-rapidapi-key" => $this->key,
            "x-rapidapi-host" => $this->host,
            "useQueryString" => true,
        ];
    }

    private function _tiktokProfile($data): array
    {
        $followers = Arr::get($data, 'stats.followerCount');
        $img = Arr::get($data, 'user.avatarLarger');
        $likes = Arr::get($data, 'stats.heartCount');
        $login = Arr::get($data, 'user.uniqueId');
        $nickname = Arr::get($data, 'user.nickname');
        $posts = Arr::get($data, 'stats.videoCount');
        $private = Arr::get($data, 'user.privateAccount');

        return compact(
            'followers',
            'img',
            'likes',
            'login',
            'nickname',
            'posts',
            'private'
        );
    }

    public function _tiktokPost($post) {
        $g = make_data_getter($post);

        $id = $g('id');
        $login = $g('authorMeta.name');

        return [
            'id' =>       $id,
            'login' =>    $login,
            'url' =>      'https://www.tiktok.com/@'.$login.'/video/'.$id,
            'img' =>      $g('covers.origin'),
            'likes' =>    $g('diggCount'),
            'reposts' =>  $g('shareCount'),
            'comments' => $g('commentCount'),
            'views' =>    $g('playCount'),
        ];
    }

    public function profile($user)
    {
        try {
            $cacheKey = __METHOD__ . $user;

            if (! Cache::has($cacheKey)) {
                $res = Http::retry(2, 500)
                    ->timeout(15)
                    ->withHeaders($this->headers())
                    ->get("https://{$this->host}/profile/$user");

                if ($res->successful()) {
                    Cache::put($cacheKey, $res->json(), $this->ttl);
                }
                else {
                    throw new ScraperException('Bad response from tiktok28');
                }
            }

            return $this->_tiktokProfile(Cache::get($cacheKey));

        }
        catch (\Throwable $e) {
            return [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ];
        }
    }

    public function feed($user)
    {
        try {
            $cacheKey = __METHOD__ . $user;

            if (! Cache::has($cacheKey)) {
                $res = Http::retry(2, 500)
                    ->timeout(15)
                    ->withHeaders($this->headers())
                    ->get("https://{$this->host}/feeds/$user");

                if ($res->successful()) {
                    Cache::put($cacheKey, $res->json(), $this->ttl);
                }
                else {
                    throw new ScraperException('Bad response from tiktok28');
                }
            }

            $res = Cache::get($cacheKey);
//            return $res;
            return array_map([$this, '_tiktokPost'], $res);

        }
        catch (\Throwable $e) {
            return [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ];
        }
    }

    public function video($url)
    {
        try {
            $cacheKey = __METHOD__ . $url;

            if (! Cache::has($cacheKey)) {
                $res = Http::retry(2, 500)
                    ->timeout(15)
                    ->withHeaders($this->headers())
                    ->get("https://{$this->host}/video", [
                        'url' => $url,
                    ]);

                if ($res->successful()) {
                    $first = $res->json()[0] ?? null;
                    Cache::put($cacheKey, $first, $this->ttl);
                }
                else {
                    throw new ScraperException('Bad response from tiktok28');
                }
            }

            $res = Cache::get($cacheKey);
            return $this->_tiktokPost($res);

        }
        catch (\Throwable $e) {
            return [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ];
        }
    }
}
