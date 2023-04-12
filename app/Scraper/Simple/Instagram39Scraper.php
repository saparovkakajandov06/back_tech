<?php

namespace App\Scraper\Simple;

use App\Exceptions\Reportable\ScraperException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

// https://rapidapi.com/premium-apis-premium-apis-default/api/instagram39/
class Instagram39Scraper implements ISimpleIgScraper
{
    use ScraperErrorTrait;

    private string $key;
    private string $host;
    private int $ttl;

    public function __construct()
    {
        $this->host = env('SCRAPER_IG39_HOST');
        $this->key  = env('SCRAPER_IG39_KEY');
        $this->ttl  = env('SCRAPER_IG39_TTL', 10);
    }

    private function headers()
    {
        return [
            'x-rapidapi-host' => $this->host,
            'x-rapidapi-key'  => $this->key,
        ];
    }

    private function httpCachedWrapper($key, $url, $params, $timeout = 15)
    {
        if (Cache::has($key)) {
            return Cache::get($key);
        }
        try {
            $res = Http::retry(2, 500)
                ->timeout($timeout)
                ->withHeaders($this->headers())
                ->get($url, $params)
                ->throw()
                ->dd();

            if (!$res->ok()) {
                throw new RequestException($res);
            }

            $data = $res->json();

            if ($data === null || !data_get($data, 'success')) { //если ответ не json || status != ok
                throw new RequestException($res);
            }

            Cache::put($key, $data, $this->ttl);
        } catch (\Throwable $e) {
            $msg = 'Bad response from instagram28 scraper';
            $statusCode = $e->getCode();
            $response = property_exists($e, 'response') ? $e->response : null;
            $responseToLog = $response ? $response->json() : [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ];
            if (data_get($response, 'data') === 'data not found!') {
                $msg = 'Instagram profile not found';
            } else {
                $this->sendScraperError(
                    $responseToLog,
                    $statusCode,
                    $url,
                    $params,
                    'no limits data'
                );
            }

            $logData = "$url, " . json_encode($params)
                . ' status code:' . $statusCode
                . ' response:' . json_encode($responseToLog);
            $this->logScraperError($logData);
            throw new ScraperException($msg);
        }

        return $data;
    }

    public function id($login)
    {
        $res = $this->httpCachedWrapper(
            key: __METHOD__ . $login,
            url: "https://{$this->host}/getProfileByUsername",
            params: ['username' => $login]
        );

        return $this->parseProfile($res['data'])['id'];
    }

    private function parseProfile($data)
    {
        $g = make_data_getter($data);
        return [
            'followers' => $g('follower_count'),
            'followings' => $g('following_count'),
            'id' => $g('pk'),
            'img' => $g('profile_pic_url'),
            'img_hd' => $g('hd_profile_pic_url_info.url'),
            'login' => $g('username'),
            'nickname' => $g('full_name'),
            'posts' => $g('media_count'),
            'private' => $g('is_private'),
        ];
    }

    public function profile($login)
    {
        $res = $this->httpCachedWrapper(
            key: __METHOD__ . $login,
            url: "https://{$this->host}/getProfileByUsername",
            params: ['username' => $login]
        );

        return $this->parseProfile($res['data']);
    }

    private function parseMedia($data)
    {
        $g = make_data_getter($data);
        return [
            'code'     => $g('shortcode'),
            'comments' => $g('edge_media_preview_comment.count'),
            'id'       => $g('id'),
            'img'      => $g('thumbnail_src', $g('display_resources.0.src')),
            'likes'    => $g('edge_media_preview_like.count'),
            'login'    => $g('owner.username'),
            'owner_id' => $g('owner.id'),
            'views'    => $g('video_view_count'),
            'type'     => match ($g('__typename')) {
                'GraphImage'   => self::IMAGE,
                'GraphVideo'   => self::VIDEO,
                'GraphSidecar' => self::SIDECAR,
                default        => null,
            },
        ];
    }

    public function media($code)
    {
        $res = $this->httpCachedWrapper(
            key: __METHOD__ . $code,
            url: "https://{$this->host}/getMediaInfo",
            params: ['short_code' => $code]
        );
        return $this->parseMedia($res['data']);
    }



    private function parseFeed($data)
    {
        $g = make_data_getter($data);
        return [
            'code'     => $g('code'),
            'comments' => $g('comment_count'),
            'id'       => $g('id'),
            'img'      => $g('image_versions2.candidates.0.url', $g('carousel_media.0.image_versions2.candidates.0.url')),
            'img_hd'   => $g('image_versions2.candidates.1.url', $g('carousel_media.0.image_versions2.candidates.1.url')) ?:  $g('image_versions2.candidates.0.url', $g('carousel_media.0.image_versions2.candidates.0.url')),
            'likes'    => $g('like_count'),
            'login'    => $g('user.username'),
            'owner_id' => $g('user.pk'),
            'views'    => $g('view_count'),
            'type'     => match ($g('media_type')) {
                1   => self::IMAGE,
                2   => self::VIDEO,
                8 => self::SIDECAR,
                default        => null,
            },
        ];
    }

    public function feed($login, $postsRequested = 12)
    {
        $id = $this->id($login);
        $posts = $this->fetchFeed($id, $postsRequested);
        return array_map(
            fn ($post) => $this->parseFeed($post),
            $posts
        );
    }

    public function feedById($id, $postsRequested = 12)
    {
        $posts = $this->fetchFeed($id, $postsRequested);
        return array_map(
            fn ($post) => $this->parseFeed($post),
            $posts
        );
    }

    private function fetchFeed($id, $postsRequested)
    {
        $posts = [];
        $hasNext = null;
        $endCursor = null;
        $params = ['user_id' => $id];
        do {
            if ($hasNext and $endCursor) {
                $params['next_max_id'] = $endCursor;
            }
            $res = $this->httpCachedWrapper(
                key: __METHOD__ . $id . '_' . count($posts),
                url: "https://{$this->host}/getFeed",
                params: $params,
                timeout: 25
            );

            $hasNext = $res['data']['more_available'];
            if (isset($res['data']['next_max_id'])) {
                $endCursor = $res['data']['next_max_id'];
            }
            $posts = array_merge($posts, $res['data']['items']);
        } while ($postsRequested > count($posts) and $hasNext);
        return array_slice($posts, 0, $postsRequested);
    }
}
