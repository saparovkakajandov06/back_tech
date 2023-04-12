<?php

namespace App\Scraper\Simple;

use App\Exceptions\Reportable\ScraperException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

// https://rapidapi.com/yuananf/api/instagram28/
class Instagram28Scraper implements ISimpleIgScraper
{
    use ScraperErrorTrait;

    private string $key;
    private string $host;
    private int $ttl;

    public function __construct($config = null)
    {
        if (!$config) {
            $config = config('scrapers.instagram.28');
        }

        $this->key  = $config['key'];
        $this->host = $config['host'];
        $this->ttl  = $config['ttl'];
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
            $cache = Cache::get($key);
            
            if ($cache === 'Instagram profile not found') {
                throw new ScraperException($cache);
            }

            return $cache;
        }

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

            if (data_get($data, 'status') !== 'ok') { // status != ok
                throw new RequestException($res);
            }

            Cache::put($key, $data, $this->ttl);
        } catch (\Throwable $e) {
            $msg = 'Bad response from instagram28 scraper';
            $textParams = json_encode($params);
            $statusCode = $e->getCode();
            $response = property_exists($e, 'response') ? $e->response : null;

            $limits = 'No limits data';
            $headers = make_data_getter($response?->headers());
            $reset = $headers('X-RateLimit-Requests-Reset.0');
            $left = $headers('X-RateLimit-Requests-Remaining.0');
            $max = $headers('X-RateLimit-Requests-Limit.0');
            if ($reset && $left) {
                $time = now(config('app.timezone'))->addSeconds($reset)->format('Y-m-d\ H:i:sP');
                $limits = "$left of $max until $time";
            }

            $responseToLog = $response
                ? $response->body()
                : describe_exception($e);
            if (
                $response
                && (data_get($response, 'error.type') === 'account_not_found' //Userinfo
                    || data_get($response, 'data.user', false) === null)
            ) {
                $msg = 'Instagram profile not found';

                Cache::put($key, $msg, 320);
            } else {
                $this->sendScraperError(
                    $responseToLog,
                    $statusCode,
                    $url,
                    $textParams,
                    $limits,
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

    public function id($login)
    {
        $res = $this->httpCachedWrapper(
            key: __METHOD__ . $login,
            url: "https://{$this->host}/getUserId",
            params: ['username' => $login]
        );
        return $res['data'];
    }

    private function parseProfile($data)
    {
        $g = make_data_getter($data);
        return [
            'followers'  => $g('edge_followed_by.count'),
            'followings' => $g('edge_follow.count'),
            'id'         => intval($g('id')),
            'img'        => $g('profile_pic_url'),
            'img_hd'     => $g('profile_pic_url_hd'),
            'login'      => $g('username'),
            'nickname'   => $g('full_name'),
            'posts'      => $g('edge_owner_to_timeline_media.count'),
            'private'    => $g('is_private'),
        ];
    }

    public function profile($login)
    {
        $dataKey = 'data.user';
        $res = $this->httpCachedWrapper(
            key: __METHOD__ . $login,
            url: "https://{$this->host}/user_info",
            params: ['user_name' => $login],
            requiredField: $dataKey
        );
        $g = make_data_getter($res);
        return $this->parseProfile($g($dataKey));
    }

    // TODO: remove
    private function parseMedia($data)
    {
        $g = make_data_getter($data);
        return [
            'code'     => $g('shortcode'),
            'comments' => $g('edge_media_to_comment.count'),
            'id'       => intval($g('id')),
            'img'      => $g('thumbnail_src', $g('display_resources.0.src')),
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

    // TODO: change to not implemented, make pipeline get data from client
    public function media($code)
    {
        $dataKey = 'data.shortcode_media';
        $res = $this->httpCachedWrapper(
            key: __CLASS__ . $code,
            url: "https://{$this->host}/media_info",
            params: ['short_code' => $code],
            requiredField: $dataKey
        );
        $g = make_data_getter($res);

        return $this->parseMedia($g($dataKey));
    }

    private function parseFeed($data)
    {
        $g = make_data_getter($data['node']);
        $code = $g('shortcode');

        Cache::put(__CLASS__ . $code, $data['node'], $this->ttl);

        return [
            'code'     => $code,
            'comments' => $g('edge_media_to_comment.count', $g('edge_media_preview_comment.count')),
            'id'       => $g('id'),
            'img'      => $g('thumbnail_resources.0.src', $g('display_resources.0.src')),
            'img_hd'   => $g('thumbnail_resources.4.src'),
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

    public function feedById($id, $postsRequested = 12)
    {
        $posts = $this->fetchFeed($id, $postsRequested);
        return array_map(
            fn ($post) => $this->parseFeed($post),
            $posts
        );
    }

    public function feed($login, $postsRequested = 12)
    {
        $id = $this->profile($login)['id'];
        $posts = $this->fetchFeed($id, $postsRequested);
        return array_map(
            fn ($post) => $this->parseFeed($post),
            $posts
        );
    }

    private function fetchFeed($id, $postsRequested)
    {
        $dataKey = 'data.user.edge_owner_to_timeline_media';
        $endCursor = null;
        $hasNext = false;
        $params = ['user_id' => $id];
        $posts = [];
        $postsLeft = $postsRequested;
        $postsParsed = 0;
        do {
            if ($hasNext and $endCursor) {
                $params['next_cursor'] = $endCursor;
            }
            $params['batch_size'] = min(50, $postsLeft);
            $res = $this->httpCachedWrapper(
                key: __METHOD__ . $id . '_' . $postsParsed,
                url: "https://{$this->host}/medias",
                params: $params,
                requiredField: $dataKey,
                timeout: 25
            );
            $g = make_data_getter($res);
            $hasNext = $g("$dataKey.page_info.has_next_page");
            $endCursor = $g("$dataKey.page_info.end_cursor");

            $posts = array_merge($posts, $g("$dataKey.edges"));
            $postsParsed = count($posts);
            $postsLeft = $postsRequested - $postsParsed;
        } while ($postsLeft > 0 and $hasNext);
        return array_slice($posts, 0, $postsRequested);
    }
}
