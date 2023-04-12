<?php

namespace App\Scraper\Simple;

use App\Exceptions\Reportable\ScraperException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

// https://rapidapi.com/herosAPI/api/instagram-data12
class Instagram12Scraper implements ISimpleIgScraper
{
    use ScraperErrorTrait;

    private string $key;
    private string $host;
    private int $ttl;

    public function __construct()
    {
        $this->key  = config('scrapers.instagram.data.key');
        $this->host = config('scrapers.instagram.data.host');
        $this->ttl  = config('scrapers.instagram.data.ttl');
    }

    /**
     * @return array
     */
    private function headers(): array
    {
        return [
            'x-rapidapi-host' => $this->host,
            'x-rapidapi-key'  => $this->key,
        ];
    }

    /**
     * @param $key
     * @param $url
     * @param $params
     * @param $requiredField
     * @param int $timeout
     * @return array|mixed
     * @throws ScraperException
     */
    private function httpCachedWrapper($key, $url, $params, $requiredField = null, int $timeout = 15): mixed
    {
        if (Cache::has($key)) {
            return Cache::get($key);
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

            Cache::put($key, $data, $this->ttl);
        } catch (\Throwable $e) {
            $msg = 'Bad response from DataIg scraper';
            $textParams = json_encode($params);
            $statusCode = $e->getCode();
            $response = $e?->response;

            $limits = 'No limits data';
            $headers = make_data_getter($response?->headers());
            $reset = $headers('X-RateLimit-All-Reset.0');
            $left = $headers('X-RateLimit-All-Remaining.0');
            $max = $headers('X-RateLimit-All-Limit.0');
            if ($reset && $left) {
                $time = now()->addSeconds($reset)->tz(config('app.timezone'))->format('Y-m-d\ H:i:sP');
                $limits = "$left of $max until $time";
            }

            $responseToLog = $response
                ? $response->body()
                : describe_exception($e);
            if (data_get($response, 'message') == 'Page not found') {
                $msg = 'Instagram profile not found';
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

    /**
     * @param $login
     * @return array
     * @throws ScraperException
     */
    public function profile($login): array
    {
        return $this->getProfile($login);
    }

    /**
     * @param $login
     * @return array
     * @throws ScraperException
     */
    private function getProfile($login): array
    {
        $res = $this->httpCachedWrapper(
            key: __METHOD__ . $login,
            url: "https://{$this->host}/profile",
            params: [
                'username' => $login,
            ],
            requiredField: ''
        );

        return $this->parseProfile($res);
    }

    /**
     * @param $data
     * @return array
     */
    private function parseProfile($data): array
    {
        $g = make_data_getter($data);

        return [
            'followers'  => $g("edge_followed_by.count"),
            'followings' => $g("edge_follow.count"),
            'img'        => $g("profile_pic_url"),
            'img_hd'     => $g("profile_pic_url_hd"),
            'login'      => $g("username"),
            'nickname'   => $g("full_name"),
            'posts'      => $g("edge_owner_to_timeline_media.count"),
            'private'    => $g("is_private"),
            'id'         => intval($g("id")),
        ];
    }

    /**
     * @param $code
     * @return array
     * @throws ScraperException
     */
    public function media($code)
    {
        $res = $this->httpCachedWrapper(
            key: __METHOD__ . $code,
            url: "https://{$this->host}/post",
            params: ['link' => 'https://www.instagram.com/p/' . $code . '/'],
            requiredField: ''
        );

        return $this->parseMedia($res);
    }

    /**
     * @param $data
     * @return array
     */
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
            'views'      => $g('video_view_count'),
            'views_real' => $g('video_view_count'),
            'type' => match ($g('__typename')) {
                'GraphImage'   => self::IMAGE,
                'GraphVideo'   => self::VIDEO,
                'GraphSidecar' => self::SIDECAR,
                default        => null,
            },
        ];
    }

    /**
     * @param $login
     * @param $postsRequested
     * @return array
     * @throws ScraperException
     */
    public function feed($login, $postsRequested = 12): array
    {
        $profile = $this->getProfile($login);
        $posts = $this->fetchFeed($login, $profile['id'], $postsRequested, null);

        return array_map(
            fn ($post) => $this->parseFeed($post),
            $posts
        );
    }

    /**
     * @param $login
     * @param $id
     * @param $postsRequested
     * @param $pageInfo
     * @return array
     * @throws ScraperException
     */
    private function fetchFeed($login, $id, $postsRequested, $pageInfo)
    {
        $posts = [];
        $hasNext = data_get($pageInfo, 'has_next_page', false);
        $endCursor = data_get($pageInfo, 'end_cursor', null);
        $params = [
            'id' => $id,
            'count' => 12,
        ];
        $feedPath = 'data.user.edge_owner_to_timeline_media';

        do {
            if ($hasNext and $endCursor) {
                $params['end_cursor'] = $endCursor;
            }
            $res = $this->httpCachedWrapper(
                key: __METHOD__ . $login . '_' . $endCursor,
                url: "https://{$this->host}/user-feeds",
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

    /**
     * @param $data
     * @return array
     */
    private function parseFeed($data)
    {
        $g = make_data_getter($data['node']);

        return [
            'code'     => $g('shortcode'),
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
}
