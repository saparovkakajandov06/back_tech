<?php

namespace App\Scraper\Simple;


use App\Exceptions\Reportable\ScraperException;
use App\Exceptions\TException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RestylerIgScraper implements ISimpleIgScraper
{
    // https://rapidapi.com/restyler/api/instagram40/

    private string $key;
    private string $host;
    private int $ttl;

    public function __construct()
    {
        $this->key = env('IG_SCRAPER_KEY');
        $this->host = env('IG_SCRAPER_HOST');
        $this->ttl = env('IG_SCRAPER_TTL', 10);
    }

    private function headers()
    {
        return [
            "x-rapidapi-key" => $this->key,
            "x-rapidapi-host" => $this->host,
        ];
    }

    private function parseProfile($data)
    {
        $g = make_data_getter($data);

        return [
            'id' => $g('id'),
            'followers' => $g('edge_followed_by.count'),
            'posts' => $g('edge_owner_to_timeline_media.count'),
            'login' => $g('username'),
            'nickname' => $g('full_name'),
            'img' => $g('profile_pic_url_hd'),
            'private' => $g('is_private'),
        ];
    }

    public function profile($login)
    {
        try {
            $start = microtime(true);

            $key = __METHOD__ . $login;

            if (!Cache::has($key)) {
                $cached = false;

                $res = Http::retry(2, 500)
                    ->timeout(15)
                    ->withHeaders($this->headers())
                    ->get("https://{$this->host}/account-info", [
                        'username' => $login,
                    ]);

                if ($res->successful()) {
                    Cache::put($key, $res->json(), $this->ttl);
                }
                else {
                    throw new ScraperException('Bad response from restyler scraper');
                }
            }
            else {
                $cached = true;
            }

            $time_elapsed_secs = microtime(true) - $start;

            $parsed = $this->parseProfile(Cache::get($key));

            $parsed['cached'] = $cached;
            $parsed['time'] = $time_elapsed_secs;

            return $parsed;

        }
        catch (\Throwable $e) {
            return [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ];
        }
    }

//    public function withTime()
//    {
//
//    }
//
//    public function profile($login)
//    {
//
//    }

    private function parseMedia($data)
    {
        $g = make_data_getter($data);

        return [
            'id' => $g('id'),
            'code' => $g('shortcode'),
            'login' => $g('owner.username'),
            'img' => $g('display_url'),
            'likes' => $g('edge_media_preview_like.count'),
            'comments' => $g('edge_media_to_parent_comment.count'),
            'views' => $g('video_view_count'),
            'type' => match ($g('__typename')) {
                'GraphImage' => self::IMAGE,
                'GraphVideo' => self::VIDEO,
                default => null,
            },
        ];
    }

    public function media($url)
    {
        try {
            $key = __METHOD__ . md5($url);

            if (!Cache::has($key)) {

                $res = Http::retry(2, 500)
                    ->timeout(15)
                    ->withHeaders($this->headers())
                    ->get("https://{$this->host}/media-info-by-url", [
                        'url' => $url,
                    ]);

                if ($res->successful()) {
                    Cache::put($key, $res->json(), $this->ttl);
                }
                else {
                    throw new ScraperException('Bad response from restyler scraper');
                }
            }

            return $this->parseMedia(Cache::get($key));

        }
        catch (\Throwable $e) {
            return [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ];
        }
    }

    private function parseFeedMedia($data)
    {
        $g = make_data_getter($data);

        return [
            'id' => $g('id'),
            'code' => $g('shortcode'),
            'login' => $g('owner.username'),
            'img' => $g('display_url'),
            'likes' => $g('edge_media_preview_like.count'),
            'comments' => $g('edge_media_to_comment.count'),
        ];
    }

    private function fetchFeed($login, $first = 12)
    {
        $userId = data_get($this->profile($login), 'id');
        $res = Http::retry(2, 500)
            ->timeout(15)
            ->withHeaders($this->headers())
            ->get("https://{$this->host}/account-medias", [
                'userid' => $userId,
                'first' => $first,
            ]);
        if ($res->successful() && 200 === $res->getStatusCode()) {
            $posts = $res->json('edges.*.node');
            return $posts;
        }
        else {
            throw new ScraperException('Bad response from restyler scraper');
        }
    }

    private function fetchFeedMulti($login, $first, $chunkSize = 50)
    {
        $userId = data_get($this->profile($login), 'id');
        $posts = [];
        $toFetch = $first;
        $endCursor = null;

        while ($toFetch > 0) {
            $limit = $toFetch <= $chunkSize ? $toFetch : $chunkSize;
            $params = [
                'userid' => $userId,
                'first' => $limit,
            ];
            if (! empty($endCursor)) {
                $params['after'] = $endCursor;
            }
            $res = Http::retry(2, 3000)
                ->withHeaders($this->headers())
                ->get("https://{$this->host}/account-medias", $params);
            if ($res->successful() && 200 === $res->getStatusCode()) {
                $endCursor = $res->json('page_info.end_cursor');
                $chunk = $res->json('edges.*.node');
                $posts = array_merge($posts, $chunk);
                $toFetch -= count($chunk);
            }
            else {
                throw new ScraperException('Bad response from restyler scraper');
            }
        }

        return $posts;
    }

    public function feed($login, $first = 12)
    {
//        try {
            $key = __METHOD__ . $login . '_' . $first;

            if (!Cache::has($key)) {
//                $posts = $this->fetchFeed($login, $first);
                $posts = $this->fetchFeedMulti($login, $first);
                Cache::put($key, $posts, $this->ttl);
            }

            return array_map([$this, 'parseFeedMedia'], Cache::get($key));
//            return array_map([$this, 'parseFeedMedia'], $this->fetchFeedMulti($login, $first));

//        } catch (\Throwable $e) {
//            return [
//                'error' => $e->getMessage(),
//                'file' => $e->getFile(),
//                'line' => $e->getLine(),
//            ];
//        }
    }
}
