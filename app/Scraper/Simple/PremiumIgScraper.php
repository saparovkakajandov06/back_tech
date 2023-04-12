<?php

namespace App\Scraper\Simple;


use App\Exceptions\Reportable\ScraperException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PremiumIgScraper implements ISimpleIgScraper
{
    //https://rapidapi.com/premium-apis-premium-apis-default/api/instagram85/

    private string $key;
    private string $host;
    private int $ttl;

    public function __construct()
    {
        $this->host = env('IPG_SCRAPER_HOST');
        $this->key  = env('IPG_SCRAPER_KEY');
        $this->ttl  = env('IPG_SCRAPER_TTL', 10);
    }

    private function headers()
    {
        return [
            "x-rapidapi-host" => $this->host,
            "x-rapidapi-key"  => $this->key,
        ];
    }

    private function parseProfile($data)
    {
        $g = make_data_getter($data['data']);
        
        return [
            'followers' => $g('figures.followers'),
            'id' => $g('id'),
            'img' => $g('profile_picture.normal'),
            'login' => $g('username'),
            'nickname' => $g('full_name'),
            'posts' => $g('figures.posts'),
            'private' => $g('is_private'),
        ];
    }

    public function profile($login)
    {
        $key = __METHOD__ . $login;
        if (!Cache::has($key)) {
            try {
                $res = Http::retry(2, 500)
                    ->timeout(15)
                    ->withHeaders($this->headers())
                    ->get("https://{$this->host}/account/{$login}/info");
            }
            catch (\Throwable $e) {
                return [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ];
            }
            if (!$res->successful()) {
                Log::channel('scraper')->info("{$login}: error response");
                throw new ScraperException('Bad response from premium instagram scraper');
            }
            if (200 !== $res->json('code')) {
                Log::channel('scraper')->info("{$login}: " . json_encode($res->json(), JSON_PRETTY_PRINT));
                throw new ScraperException('login invalid or private');
            }
            Cache::put($key, $res->json(), $this->ttl);
        }
        return $this->parseProfile(Cache::get($key));
    }

    private function parseMedia($data)
    {
        $g = make_data_getter($data['data']);
        
        return [
            'code'     => $g('short_code'),
            'comments' => $g('figures.comments_count'),
            'id'       => $g('id'),
            'img'      => $g('images.square.0'),
            'likes'    => $g('figures.likes_count'),
            'login'    => $g('owner.username'),
            'owner_id' => $g('owner.id'),
            'views'    => $g('figures.video_views'),
            'type'     => match ($g('type')) {
                'image'   => self::IMAGE,
                'video'   => self::VIDEO,
                'sidecar' => self::SIDECAR,
                default   => null,
            },
        ];
    }

    public function media($url)
    {
        $key = __METHOD__ . md5($url);
        if (!Cache::has($key)) {
            try {
                $res = Http::retry(2, 500)
                    ->timeout(15)
                    ->withHeaders($this->headers())
                    ->get("https://{$this->host}/media/{$url}", ['by' => 'url']);
            }
            catch (\Throwable $e) {
                return [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ];
            }
            if (!$res->successful()) {
                Log::channel('scraper')->info("{$url}: error response");
                throw new ScraperException('Bad response from premium instagram scraper');
            }
            if (200 !== $res->json('code')) {
                Log::channel('scraper')->info($url . ': ' . json_encode($res->json(), JSON_PRETTY_PRINT));
                throw new ScraperException('invalid instagram link');
            }
            Cache::put($key, $res->json(), $this->ttl);
        }
        return $this->parseMedia(Cache::get($key));
    }

    public function feed($login, $postCount = 12)
    {
        $key = __METHOD__ . $login . '_' . $postCount;
        if (!Cache::has($key)) {
            $posts = $this->fetchFeed($login, $postCount);
            Cache::put($key, $posts, $this->ttl);
        }
        return Cache::get($key);
    }

    private function fetchFeed($login, $postCount)
    {
        $posts = [];
        $hasNext = null;
        $nextPage = null;
        $params = ['by' => 'username'];

        do {
            if ($hasNext and $nextPage) {
                $params['pageId'] = $nextPage;
            }

            $res = Http::retry(2, 500)
                ->timeout(15)
                ->withHeaders($this->headers())
                ->get("https://{$this->host}/account/{$login}/feed", $params);

            if (!$res->successful()) {
                Log::channel('scraper')->info("{$login}({$postCount}): error response");
                throw new ScraperException('Bad response from premium instagram scraper');
            }
            if (200 !== $res->json('code')) {
                Log::channel('scraper')->info("{$login}({$postCount}): " . json_encode($res->json(), JSON_PRETTY_PRINT));
                throw new ScraperException('login invalid or private');
            }
            $hasNext = $res->json('meta.has_next');
            $nextPage = $res->json('meta.next_page');
            $posts = array_merge(
                $posts,
                array_map(
                    fn($post) => $this->parseMedia(['data' => $post]),
                    $res->json('data.*')
                )
            );
        } while ($postCount > count($posts) and $hasNext);
        return $postCount >= count($posts)
            ? $posts
            : array_chunk($posts, $postCount)[0];
    }
}
