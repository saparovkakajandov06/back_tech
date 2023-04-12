<?php

namespace App\Scraper\Simple;

use App\Exceptions\Reportable\ScraperException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

// https://rapidapi.com/JoTucker/api/tiktok-scraper2
class JoTiktokScraper implements ISimpleTiktokScraper
{
    use ScraperErrorTrait;

    private string $key;
    private string $host;
    private int $ttl;

    public function __construct($config = null)
    {
        if (!$config) {
            $config = config('scrapers.tiktok.jo');
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

            if (
                !empty($params['user_name']) &&
                !empty($data['userInfo']['user']['uniqueId']) &&
                strcmp($params['user_name'], $data['userInfo']['user']['uniqueId']) !== 0
            ) {
                throw new RequestException($res);
            }

            Cache::put($key, $data, $this->ttl);
        } catch (\Throwable $e) {
            $msg = 'Bad response from JoTiktok scraper';
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
            $data = $response?->json();
            if (
                (!empty($params['user_name']) &&
                    !empty($data['userInfo']['user']['uniqueId']) &&
                    strcmp($params['user_name'], $data['userInfo']['user']['uniqueId']) !== 0
                ) ||
                empty((array)$data) ||
                data_get($response, 'message') === 'User not found'
            ) { // message == User not found
                $msg = 'Tiktok profile not found';
            } else { // code 0 => любой эксепшн, ConnectionException
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

    private function parseProfile($data): array
    {
        $g = make_data_getter($data);

        return [
            'followers' => $g('stats.followerCount'),
            'following' => $g('stats.followingCount'),
            'id'        => $g('user.id'),
            'img_hd'    => $g('user.avatarLarger'),
            'img'       => $g('user.avatarMedium'),
            'likes'     => $g('stats.heartCount'),
            'login'     => $g('user.uniqueId'),
            'nickname'  => $g('user.nickname'),
            'posts'     => $g('stats.videoCount'),
            'private'   => $g('user.privateAccount')
        ];
    }

    public function profile($login)
    {
        $dataKey = 'userInfo';
        $res = $this->httpCachedWrapper(
            key: __METHOD__ . $login,
            url: "https://{$this->host}/user/info",
            params: ['user_name' => $login],
            requiredField: $dataKey
        );

        $g = make_data_getter($res);
        return $this->parseProfile($g($dataKey));
    }

    // {
    //   "itemList": [
    //     {
    //       "id": "7193423374037863726",
    //       "video": {
    //         "cover": "https://p16-sign.tiktokcdn-us.com/obj/tos-useast5-p-0068-tx/183b157208894dadbcc0b701feb59bd8?x-expires=1674968400&x-signature=eAu9ScJKnMkUVc8LOr8sxPDHiuM%3D"
    //       },
    //       "stats": {
    //         "commentCount": 3694,
    //         "diggCount": 2741,
    //         "playCount": 70700,
    //         "shareCount": 72
    //       }
    //     },
    //     {
    //       "id": "7192991227384532266",
    //       "video": {
    //         "cover": "https://p16-sign.tiktokcdn-us.com/obj/tos-useast5-p-85c255-tx/f46d1f75e9c448f0a2d6f440abf1b81c_1674748812?x-expires=1674968400&x-signature=%2FytpdeNehG%2BicO3fw5DPYrFAYyU%3D"
    //       },
    //       "stats": {
    //         "commentCount": 2467,
    //         "diggCount": 3370,
    //         "playCount": 113400,
    //         "shareCount": 78
    //       }
    //     },
    //   ],
    //   "statusCode": 0,
    //   "status_code": 0
    // }

    private function parseFeed($login, $post)
    {
        $g = make_data_getter($post);

        $id = $g('id');
        $url = "https://www.tiktok.com/@{$login}/video/{$id}";

        Cache::put(__CLASS__ . $url, ['itemStruct' => $post], $this->ttl);

        return [
            'comments' => $g('stats.commentCount'),
            'id'       => $id,
            'img'      => $g('video.cover'),
            'likes'    => $g('stats.diggCount'),
            'login'    => $login,
            'reposts'  => $g('stats.shareCount'),
            'url'      => $url,
            'views'    => $g('stats.playCount'),
        ];
    }

    public function feed($login, $postCount = 12)
    {
        if (str_starts_with($login, '@')) {
            $login = substr($login, 1);
        }

        $dataKey = 'itemList';
        $res = $this->httpCachedWrapper(
            key: __METHOD__ . $login,
            url: "https://{$this->host}/user/videos",
            params: ['user_name' => $login],
            requiredField: $dataKey,
            timeout: 25
        );

        $posts = $res[$dataKey] ?: [];

        if (count($posts) > $postCount) {
            $posts = array_slice($posts, 0, $postCount);
        }

        $callback = function ($post) use ($login) {
            return $this->parseFeed($login, $post);
        };

        return array_map($callback, $posts);
    }

    // {
    //   "itemStruct": {
    //     "author": {
    //       "avatarLarger": "https://p16-sign-va.tiktokcdn.com/tos-maliva-avt-0068/1971e99be0d67160f34f39fb1d66a0e5~c5_1080x1080.jpeg?x-expires=1675119600&x-signature=JOJWsX8eK6FGZUM%2BYaiF7DJWLkY%3D",
    //       "avatarMedium": "https://p16-sign-va.tiktokcdn.com/tos-maliva-avt-0068/1971e99be0d67160f34f39fb1d66a0e5~c5_720x720.jpeg?x-expires=1675119600&x-signature=2cQ0qnDcZXkunKpn%2FcanVZMkqc4%3D",
    //       "avatarThumb": "https://p16-sign-va.tiktokcdn.com/tos-maliva-avt-0068/1971e99be0d67160f34f39fb1d66a0e5~c5_100x100.jpeg?x-expires=1675119600&x-signature=VqPBlbnQCRPw9NS3hPMFXaUIuk4%3D",
    //       "commentSetting": 0,
    //       "duetSetting": 0,
    //       "ftc": false,
    //       "id": "107955",
    //       "isADVirtual": false,
    //       "nickname": "TikTok",
    //       "openFavorite": false,
    //       "privateAccount": false,
    //       "relation": 0,
    //       "secUid": "MS4wLjABAAAAv7iSuuXDJGDvJkmH_vz1qkDZYo1apxgzaxdBSeIuPiM",
    //       "secret": false,
    //       "signature": "the 🕰️ app",
    //       "stitchSetting": 0,
    //       "uniqueId": "tiktok",
    //       "verified": true
    //     },
    //     "challenges": [
    //       {
    //         "coverLarger": "",
    //         "coverMedium": "",
    //         "coverThumb": "",
    //         "desc": "",
    //         "id": "17684529",
    //         "profileLarger": "",
    //         "profileMedium": "",
    //         "profileThumb": "",
    //         "title": "throwbacktrend"
    //       }
    //     ],
    //     "createTime": "1623961812",
    //     "desc": "this #ThrowbackTrend goes to the one-and-only @mikeposner 🎉 watch him reflect on making his iconic song & share his fave video from the trend",
    //     "digged": false,
    //     "duetDisplay": 0,
    //     "duetEnabled": true,
    //     "forFriend": false,
    //     "id": "6974862859000073478",
    //     "itemCommentStatus": 0,
    //     "music": {
    //       "authorName": "TikTok",
    //       "coverLarge": "https://p16-sign-va.tiktokcdn.com/tos-maliva-avt-0068/1971e99be0d67160f34f39fb1d66a0e5~c5_1080x1080.jpeg?x-expires=1675119600&x-signature=JOJWsX8eK6FGZUM%2BYaiF7DJWLkY%3D",
    //       "coverMedium": "https://p16-sign-va.tiktokcdn.com/tos-maliva-avt-0068/1971e99be0d67160f34f39fb1d66a0e5~c5_720x720.jpeg?x-expires=1675119600&x-signature=2cQ0qnDcZXkunKpn%2FcanVZMkqc4%3D",
    //       "coverThumb": "https://p16-sign-va.tiktokcdn.com/tos-maliva-avt-0068/1971e99be0d67160f34f39fb1d66a0e5~c5_100x100.jpeg?x-expires=1675119600&x-signature=VqPBlbnQCRPw9NS3hPMFXaUIuk4%3D",
    //       "duration": 45,
    //       "id": "6974862787264793350",
    //       "original": false,
    //       "playUrl": "https://sf16-ies-music-va.tiktokcdn.com/obj/musically-maliva-obj/6975238648522722054.mp3",
    //       "title": "original sound"
    //     },
    //     "officalItem": false,
    //     "originalItem": false,
    //     "privateItem": false,
    //     "secret": false,
    //     "shareEnabled": true,
    //     "stats": {
    //       "commentCount": 9506,
    //       "diggCount": 87700,
    //       "playCount": 6300000,
    //       "shareCount": 3649
    //     },
    //     "stitchDisplay": 0,
    //     "stitchEnabled": true,
    //     "textExtra": [
    //       {
    //         "awemeId": "",
    //         "end": 57,
    //         "hashtagName": "",
    //         "isCommerce": false,
    //         "secUid": "MS4wLjABAAAA8tCBr7c1atjcJhG5UzxvZNrQ0xexZj8yemBh1mREGUVSENujdih1deq2CNKDteKi",
    //         "start": 46,
    //         "subType": 0,
    //         "type": 0,
    //         "userId": "107309388175933440",
    //         "userUniqueId": "mikeposner"
    //       },
    //       {
    //         "awemeId": "",
    //         "end": 20,
    //         "hashtagId": "17684529",
    //         "hashtagName": "throwbacktrend",
    //         "isCommerce": false,
    //         "start": 5,
    //         "subType": 0,
    //         "type": 1
    //       }
    //     ],
    //     "video": {
    //       "bitrate": 1157866,
    //       "bitrateInfo": [
    //         {
    //           "Bitrate": 1157866,
    //           "CodecType": "h264",
    //           "GearName": "normal_720_0",
    //           "PlayAddr": {
    //             "DataSize": 6569879,
    //             "FileCs": "c:0-28242-0f70",
    //             "FileHash": "c6b0d95f912aa683098ba55fd11544a4",
    //             "Uri": "v09044g40000c35r18a9ccalqjgfv110",
    //             "UrlKey": "v09044g40000c35r18a9ccalqjgfv110_h264_720p_1157866",
    //             "UrlList": [
    //               "https://v19-webapp-prime.tiktok.com/video/tos/useast2a/tos-useast2a-ve-0068c004/464e93237aef4f0ba02cc1a505625635/?a=1988&ch=0&cr=0&dr=0&lr=tiktok_m&cd=0%7C0%7C1%7C0&cv=1&br=2260&bt=1130&cs=0&ds=3&ft=4fUEKMzm8Zmo0AIu764jV7PxJpWrKsdm&mime_type=video_mp4&qs=0&rc=aWUzO2gzNzxmN2g1ZzNpZkBpMzRpbW9mPDt1NjMzNzczM0BeMWJgLV5gNTIxNGMxYDZeYSMuc2RuXmBeLjJgLS1kMTZzcw%3D%3D&btag=80000&expire=1674970507&l=2023012823342103CD7988416BA7DDE8B1&ply_type=2&policy=2&signature=a7f7a2014626fc131e57b6691f2872ae&tk=tt_chain_token",
    //               "https://v16-webapp-prime.tiktok.com/video/tos/useast2a/tos-useast2a-ve-0068c004/464e93237aef4f0ba02cc1a505625635/?a=1988&ch=0&cr=0&dr=0&lr=tiktok_m&cd=0%7C0%7C1%7C0&cv=1&br=2260&bt=1130&cs=0&ds=3&ft=4fUEKMzm8Zmo0AIu764jV7PxJpWrKsdm&mime_type=video_mp4&qs=0&rc=aWUzO2gzNzxmN2g1ZzNpZkBpMzRpbW9mPDt1NjMzNzczM0BeMWJgLV5gNTIxNGMxYDZeYSMuc2RuXmBeLjJgLS1kMTZzcw%3D%3D&btag=80000&expire=1674970507&l=2023012823342103CD7988416BA7DDE8B1&ply_type=2&policy=2&signature=a7f7a2014626fc131e57b6691f2872ae&tk=tt_chain_token"
    //             ]
    //           },
    //           "QualityType": 10
    //         }
    //       ],
    //       "codecType": "h264",
    //       "cover": "https://p16-sign-va.tiktokcdn.com/obj/tos-maliva-p-0068/a1fe4497118a4218951e155e075b0f3e_1623961818?x-expires=1674968400&x-signature=VlfPN3Nyv%2BMM%2B1pig4ZzZKc%2BYVs%3D",
    //       "definition": "720p",
    //       "downloadAddr": "https://v19-webapp-prime.tiktok.com/video/tos/useast2a/tos-useast2a-ve-0068c004/464e93237aef4f0ba02cc1a505625635/?a=1988&ch=0&cr=0&dr=0&lr=tiktok_m&cd=0%7C0%7C1%7C0&cv=1&br=2260&bt=1130&cs=0&ds=3&ft=4fUEKMzm8Zmo0AIu764jV7PxJpWrKsdm&mime_type=video_mp4&qs=0&rc=aWUzO2gzNzxmN2g1ZzNpZkBpMzRpbW9mPDt1NjMzNzczM0BeMWJgLV5gNTIxNGMxYDZeYSMuc2RuXmBeLjJgLS1kMTZzcw%3D%3D&btag=80000&expire=1674970507&l=2023012823342103CD7988416BA7DDE8B1&ply_type=2&policy=2&signature=a7f7a2014626fc131e57b6691f2872ae&tk=tt_chain_token",
    //       "duration": 45,
    //       "dynamicCover": "https://p16-sign-va.tiktokcdn.com/obj/tos-maliva-p-0068/59682f5906564ca9856a8aee83335b82_1624049792?x-expires=1674968400&x-signature=0%2BGv75Ctq0%2F%2FW287SDxx8U%2F%2Fhqk%3D",
    //       "encodeUserTag": "",
    //       "encodedType": "normal",
    //       "format": "mp4",
    //       "height": 1024,
    //       "id": "6974862859000073478",
    //       "originCover": "https://p16-sign-va.tiktokcdn.com/obj/tos-maliva-p-0068/3e4c3f5cc81d4818900d06d7c8e114c6_1623961816?x-expires=1674968400&x-signature=EWmhkzOJaNy2ObrI26whw6K10OQ%3D",
    //       "playAddr": "https://v19-webapp-prime.tiktok.com/video/tos/useast2a/tos-useast2a-ve-0068c004/464e93237aef4f0ba02cc1a505625635/?a=1988&ch=0&cr=0&dr=0&lr=tiktok_m&cd=0%7C0%7C1%7C0&cv=1&br=2260&bt=1130&cs=0&ds=3&ft=4fUEKMzm8Zmo0AIu764jV7PxJpWrKsdm&mime_type=video_mp4&qs=0&rc=aWUzO2gzNzxmN2g1ZzNpZkBpMzRpbW9mPDt1NjMzNzczM0BeMWJgLV5gNTIxNGMxYDZeYSMuc2RuXmBeLjJgLS1kMTZzcw%3D%3D&btag=80000&expire=1674970507&l=2023012823342103CD7988416BA7DDE8B1&ply_type=2&policy=2&signature=a7f7a2014626fc131e57b6691f2872ae&tk=tt_chain_token",
    //       "ratio": "720p",
    //       "subtitleInfos": [
    //         {
    //           "Format": "webvtt",
    //           "LanguageCodeName": "eng-US",
    //           "LanguageID": "2",
    //           "Size": 0,
    //           "Source": "ASR",
    //           "Url": "https://v16-webapp.tiktok.com/edab60c34097c4a3ea6aa196b4fce524/63d6058b/video/tos/useast2a/tos-useast2a-v-0068/1fcd1de7da814b05abe2c7c85b501415/?a=1988&ch=0&cr=0&dr=0&lr=tiktok_m&cd=0%7C0%7C1%7C0&cv=1&br=17846&bt=8923&cs=0&ds=4&ft=4b~OyMzm8Zmo0AIu764jV7PxJpWrKsdm&mime_type=video_mp4&qs=13&rc=MzRpbW9mPDt1NjMzNzczM0BpMzRpbW9mPDt1NjMzNzczM0Auc2RuXmBeLjJgLS1kMTZzYSMuc2RuXmBeLjJgLS1kMTZzcw%3D%3D&l=2023012823342103CD7988416BA7DDE8B1&btag=40000",
    //           "UrlExpire": 1674970507,
    //           "Version": "1"
    //         },
    //         {
    //           "Format": "webvtt",
    //           "LanguageCodeName": "por-PT",
    //           "LanguageID": "8",
    //           "Size": 0,
    //           "Source": "MT",
    //           "Url": "https://v16-webapp.tiktok.com/50b90c47c274383ae8068f8c915b35b3/63d6058b/video/tos/useast2a/tos-useast2a-v-0068/70a265ec1fc8424f9980ad254ad30c66/?a=1988&ch=0&cr=0&dr=0&lr=tiktok_m&cd=0%7C0%7C1%7C0&cv=1&br=17846&bt=8923&cs=0&ds=4&ft=4b~OyMzm8Zmo0AIu764jV7PxJpWrKsdm&mime_type=video_mp4&qs=13&rc=MzRpbW9mPDt1NjMzNzczM0BpMzRpbW9mPDt1NjMzNzczM0Auc2RuXmBeLjJgLS1kMTZzYSMuc2RuXmBeLjJgLS1kMTZzcw%3D%3D&l=2023012823342103CD7988416BA7DDE8B1&btag=40000",
    //           "UrlExpire": 1674970507,
    //           "Version": "4"
    //         },
    //         {
    //           "Format": "webvtt",
    //           "LanguageCodeName": "cmn-Hans-CN",
    //           "LanguageID": "1",
    //           "Size": 0,
    //           "Source": "MT",
    //           "Url": "https://v16-webapp.tiktok.com/21d82ac11b635aadbb472b9697dd4a71/63d6058b/video/tos/alisg/tos-alisg-pv-0037/e8871efb46f04ead9a0b5e80ff771e6d/?a=1988&ch=0&cr=0&dr=0&lr=tiktok_m&cd=0%7C0%7C1%7C0&cv=1&br=17846&bt=8923&cs=0&ds=4&ft=4b~OyMzm8Zmo0AIu764jV7PxJpWrKsdm&mime_type=video_mp4&qs=13&rc=MzRpbW9mPDt1NjMzNzczM0BpMzRpbW9mPDt1NjMzNzczM0Auc2RuXmBeLjJgLS1kMTZzYSMuc2RuXmBeLjJgLS1kMTZzcw%3D%3D&l=2023012823342103CD7988416BA7DDE8B1&btag=40000",
    //           "UrlExpire": 1674970507,
    //           "Version": "4"
    //         }
    //       ],
    //       "videoQuality": "normal",
    //       "volumeInfo": {
    //         "Loudness": -27.8,
    //         "Peak": 0.2851
    //       },
    //       "width": 576,
    //       "zoomCover": {
    //         "240": "https://p16-sign-va.tiktokcdn.com/tos-maliva-p-0068/a1fe4497118a4218951e155e075b0f3e_1623961818~tplv-f5insbecw7-1:240:240.jpeg?x-expires=1674968400&x-signature=0E4ZfGR06LuE36UZyNGMU6yuNdw%3D",
    //         "480": "https://p16-sign-va.tiktokcdn.com/tos-maliva-p-0068/a1fe4497118a4218951e155e075b0f3e_1623961818~tplv-f5insbecw7-1:480:480.jpeg?x-expires=1674968400&x-signature=25xwGa0zH5bnfEIHuOGCFfRlwPQ%3D",
    //         "720": "https://p16-sign-va.tiktokcdn.com/tos-maliva-p-0068/a1fe4497118a4218951e155e075b0f3e_1623961818~tplv-f5insbecw7-1:720:720.jpeg?x-expires=1674968400&x-signature=uwGxt1IhQWHggHr4XzSXFoW9v%2B0%3D",
    //         "960": "https://p16-sign-va.tiktokcdn.com/tos-maliva-p-0068/a1fe4497118a4218951e155e075b0f3e_1623961818~tplv-f5insbecw7-1:960:960.jpeg?x-expires=1674968400&x-signature=UperNUoUfEkQmT11%2FVaUZer1%2F2Q%3D"
    //       }
    //     }
    //   }
    // }
    // url: 'https://tiktok-scraper2.p.rapidapi.com/video/info'
    // TODO: remove
    private function parsePost($post)
    {
        $g = make_data_getter($post);

        $id    = $g('id');
        $login = $g('author.uniqueId');
        $url   = "https://www.tiktok.com/@{$login}/video/{$id}";

        return [
            'comments' => $g('stats.commentCount'),
            'id'       => $id,
            'img'      => $g('video.cover'),
            'likes'    => $g('stats.diggCount'),
            'login'    => $login,
            'reposts'  => $g('stats.shareCount'),
            'url'      => $url,
            'views'    => $g('stats.playCount'),
        ];
    }

    // TODO: change to not implemented, make pipeline get data from client
    public function video($url)
    {
        $dataKey = 'itemStruct';
        $res = $this->httpCachedWrapper(
            key: __CLASS__ . $url,
            url: "https://{$this->host}/video/info",
            params: ['video_url' => $url],
            requiredField: $dataKey
        );

        $g = make_data_getter($res);
        return $this->parsePost($g($dataKey));
    }
}
