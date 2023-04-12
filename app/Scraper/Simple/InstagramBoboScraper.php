<?php

namespace App\Scraper\Simple;

use App\Exceptions\Reportable\ScraperException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

// https://rapidapi.com/arraybobo/api/instagram-scraper-2022/
class InstagramBoboScraper implements ISimpleIgScraper
{
    use ScraperErrorTrait;

    private string $key;
    private string $host;
    private int $ttl;

    public function __construct()
    {
        $this->key  = config('scrapers.instagram.bobo.key');
        $this->host = config('scrapers.instagram.bobo.host');
        $this->ttl  = config('scrapers.instagram.bobo.ttl');
    }

    private function headers()
    {
        return [
            'x-rapidapi-host' => $this->host,
            'x-rapidapi-key'  => $this->key,
        ];
    }

    private function httpCachedWrapper($key, $url, $params, $requiredField = null, $timeout = 15)
    {
        if (Cache::has($key)) {
            return Cache::get($key);
        }
        $isPostInfoUrl = preg_match('/post_info/', $url);
        try {
            $res = Http::retry(2, 500)
                ->timeout($timeout)
                ->withHeaders($this->headers())
                ->get($url, $params)
                ->throw();

            if (!$res->ok()) {
                throw new RequestException($res);
            }

            $data = $res->json();

            if (
                $data === null // ответ не json
                || (isset($requiredField) && !data_get($data, $requiredField)) // нет необходимого поля
            ) {
                throw new RequestException($res);
            }

            Cache::put($key, $data, $this->ttl);
        } catch (\Throwable $e) {
            $msg = 'Bad response from BoboIg scraper';
            $textParams = json_encode($params);
            $statusCode = $e->getCode();
            $response = property_exists($e, 'response') ? $e->response : null;

            $limits = 'No limits data';
            $headers = make_data_getter($response?->headers());
            $reset = $headers('X-RateLimit-All-Reset.0');
            $left = $headers('X-RateLimit-All-Remaining.0');
            $max = $headers('X-RateLimit-All-Limit.0');
            if ($reset && $left) {
                $time = now(config('app.timezone'))->addSeconds($reset)->format('Y-m-d\ H:i:sP');
                $limits = "$left of $max until $time";
            }

            $responseToLog = $response
                ? $response->body()
                : describe_exception($e);
            if (!$isPostInfoUrl && data_get($response, 'message') == 'Page not found') {
                $msg = 'Instagram profile not found';
                Cache::put($key, $msg, 320);
            } else {
                $this->sendScraperError(
                    $responseToLog,
                    $statusCode,
                    $url,
                    $textParams,
                    $limits
                );
            }

            $logData = "$url, " . $textParams
                . ' status code:' . $statusCode
                . ' response:' . $responseToLog
                . ' limits:' . $limits;
            $this->logScraperError($logData);
            throw new ScraperException($msg);
        }

        return $data;
    }

    // TODO: remove
    private function parseMedia($data)
    {
        $g = make_data_getter($data);
        return [
            'code'       => $g('shortcode'),
            'comments'   => $g('edge_media_to_comment.count'),
            'id'         => intval($g('id')),
            'img'        => $g('thumbnail_src', $g('display_resources.0.src')),
            'likes'      => $g('edge_media_preview_like.count'),
            'login'      => $g('owner.username'),
            'owner_id'   => $g('owner.id'),
            'views'      => $g('video_play_count'),
            'views_real' => $g('video_view_count'),
            'type' => match ($g('__typename')) {
                'GraphImage'   => self::IMAGE,
                'GraphVideo'   => self::VIDEO,
                'GraphSidecar' => self::SIDECAR,
                default        => null,
            },
        ];
    }

    // TODO: change to not implemented, make pipeline get data from client
    public function media($code)
    {
        $res = $this->httpCachedWrapper(
            key: __CLASS__ . $code,
            url: "https://{$this->host}/ig/post_info/",
            params: ['shortcode' => $code],
            requiredField: 'id'
        );

        return $this->parseMedia($res);
    }

    private function parseProfile($data, $feed = false)
    {
        $g = make_data_getter($data['data']['user']);

        $feedPath = 'edge_owner_to_timeline_media';
        $arr = [
            'followers'  => $g('edge_followed_by.count'),
            'followings' => $g('edge_follow.count'),
            'id'         => intval($g('id')),
            'img'        => $g('profile_pic_url'),
            'img_hd'     => $g('profile_pic_url_hd'),
            'login'      => $g('username'),
            'nickname'   => $g('full_name'),
            'posts'      => $g("$feedPath.count"),
            'private'    => $g('is_private'),
        ];

        if ($feed) {
            $arr['page_info'] = $g("$feedPath.page_info");
            $arr['posts_data'] = $g("$feedPath.edges");
        }
        return $arr;
    }

    public function profileFeed($login, $feed = false)
    {
        $res = $this->httpCachedWrapper(
            key: __METHOD__ . $login,
            url: "https://{$this->host}/ig/web_profile_info/",
            params: ['user' => $login],
            requiredField: 'data.user'
        );

        return $this->parseProfile($res, $feed);
    }

    public function profile($login)
    {
        return $this->profileFeed($login);
    }

    private function parseFeed($data)
    {
        $g = make_data_getter($data['node']);
        $code = $g('shortcode');
        
        Cache::put(__CLASS__ . $code, $data['node'], $this->ttl);

        return [
            'code'     => $code,
            'comments' => $g('edge_media_to_comment.count'),
            'id'       => intval($g('id')),
            'img'      => $g('thumbnail_resources.0.src', $g('display_resources.0.src')),
            'img_hd'   => $g('thumbnail_resources.4.src'),
            'likes'    => $g('edge_media_preview_like.count'),
            'login'    => $g('owner.username'),
            'owner_id' => intval($g('owner.id')),
            'views'    => $g('video_view_count'),
            'type'     => match ($g('__typename')) {
                'GraphImage'   => self::IMAGE,
                'GraphVideo'   => self::VIDEO,
                'GraphSidecar' => self::SIDECAR,
                default        => null,
            },
        ];
    }

    public function feed($login, $postsRequested = 12)
    {
        $profile = $this->profileFeed($login, true);
        // if (($c = data_get($profile, 'posts', 0)) < $postsRequested) {
        //     throw new ScraperException("Not enough posts: $c < " . $postsRequested);
        // }

        $posts = data_get($profile, 'posts_data', []) ?: [];

        if (count($posts) >= $postsRequested) {
            $posts = array_slice($posts, 0, $postsRequested);
        } elseif (data_get($profile, 'page_info.has_next_page', true)) {
            $pageInfo = data_get($profile, 'page_info', null);
            $morePosts = $this->fetchFeed($login, $postsRequested - count($posts), $pageInfo);
            $posts = array_merge($posts, $morePosts);
        }

        return array_map(
            fn ($post) => $this->parseFeed($post),
            $posts
        );
    }

    private function fetchFeed($login, $postsRequested, $pageInfo)
    {
        $posts = [];
        $hasNext = data_get($pageInfo, 'has_next_page', false);
        $endCursor = data_get($pageInfo, 'end_cursor', null);
        $params = ['user' => $login];
        $feedPath = 'data.user.edge_owner_to_timeline_media';

        do {
            if ($hasNext and $endCursor) {
                $params['end_cursor'] = $endCursor;
            }
            $res = $this->httpCachedWrapper(
                key: __METHOD__ . $login . '_' . $endCursor,
                url: "https://{$this->host}/ig/posts_username/",
                params: $params,
                requiredField: $feedPath,
                timeout: 25
            );
            $g = make_data_getter($res);
            $hasNext = $g("$feedPath.page_info.has_next_page");
            $endCursor = $g("$feedPath.page_info.end_cursor");

            $posts = array_merge($posts, $g("$feedPath.edges"));
        } while ($postsRequested > count($posts) and $hasNext);
        return array_slice($posts, 0, $postsRequested);
    }
}
