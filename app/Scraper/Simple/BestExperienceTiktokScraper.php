<?php

namespace App\Scraper\Simple;

use App\Exceptions\Reportable\NotImplementedException;
use App\Exceptions\Reportable\ScraperException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

// https://rapidapi.com/neotank/api/tiktok-bulletproof
class BestExperienceTiktokScraper implements ISimpleTiktokScraper
{
    use ScraperErrorTrait;

    private string $key;
    private string $host;
    private int $ttl;

    public function __construct($config = null)
    {
        if (!$config) {
            $config = config('scrapers.tiktok.bestexperience');
        }
        $this->key  = $config['key'];
        $this->host = $config['host'];
        $this->ttl  = $config['ttl'];
    }

    private function headers()
    {
        return [
            'x-rapidapi-key'  => $this->key,
            'x-rapidapi-host' => $this->host,
        ];
    }

    private function httpCachedWrapper($key, $url, $params, $requiredField = null, $timeout = 15)
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

            if (!$res->ok()) { // status != 200 ok
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
            $msg = 'Bad response from BestExperience scraper';
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
            if ($statusCode == 404 && data_get($response, 'status') == 'fail') { //be - http code - 400 и status == error
                $msg = 'Tiktok profile not found';
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

        return $res;
    }

    private function parseProfile($data): array
    {
        $g = make_data_getter($data);

        return [
            'id'        => $g('user.id', 0),
            'followers' => $g('stats.followerCount', 0),
            'following' => $g('stats.followingCount', 0),
            'img'       => $g('user.avatarMedium'),
            'img_hd'    => $g('user.avatarLarger'),
            'likes'     => $g('stats.diggCount', 0),
            'login'     => $g('user.uniqueId'),
            'nickname'  => $g('user.nickname'),
            'posts'     => $g('stats.videoCount', 0),
            'private'   => $g('user.secret', false)
        ];
    }

    public function profile($user)
    {
        $res = $this->httpCachedWrapper(
            key: __METHOD__ . $user,
            url: "https://{$this->host}/user-info",
            params: ['username' => $user],
            requiredField: 'user',
        );
        return $this->parseProfile($res);
    }

    private function parsePost($post)
    {
        $g = make_data_getter($post);

        $id = $g('id');
        $login = $g('author');

        return [
            'comments' => $g('stats.commentCount'),
            'id'       => $id,
            'img'      => $g('video.cover'),
            'likes'    => $g('stats.diggCount'),
            'login'    => $login,
            'reposts'  => $g('stats.shareCount'),
            'url'      => "https://www.tiktok.com/@{$login}/video/{$id}",
            'views'    => $g('stats.playCount'),
        ];
    }

    public function feed($user, $postCount = 12)
    {
        $cacheKey = __METHOD__ . $user;
        $dataKey = 'feed';
        $res = $this->httpCachedWrapper(
            $cacheKey,
            "https://{$this->host}/user-feed",
            ['username' => $user],
            $dataKey,
            25
        );

        $posts = $res[$dataKey] ?: [];

        if (count($posts) > $postCount) {
            $posts = array_slice($posts, 0, $postCount);
        }

        return array_map([$this, 'parsePost'], $posts);
    }

    // TODO: make pipeline get data from client
    public function video($url)
    {
        $method = static::class . '::' . __FUNCTION__;
        throw new NotImplementedException($method);
    }
}
