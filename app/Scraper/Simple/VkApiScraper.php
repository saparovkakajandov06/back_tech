<?php

namespace App\Scraper\Simple;

use Illuminate\Support\Facades\Http;
use App\Exceptions\Reportable\ScraperException;
use Illuminate\Support\Facades\Cache;

class VkApiScraper implements ISimpleVkScraper
{
    private string $key;
    private string $host;
    private string $version;

    public function __construct()
    {
        $this->key = env('VK_API_SCRAPER_KEY');
        $this->host = env('VK_API_SCRAPER_HOST');
        $this->version = env('VK_API_SCRAPER_VERSION');
    }

    private function parseProfile($data, $type)
    {
        if(!isset($data[0])){
            throw new ScraperException('Bad response from vk api scraper');
        }
        
        $g = make_data_getter($data[0]);

        if ($type === 'user') {
            $res = [
                'id' => $g('id'),
                'first_name' => $g('first_name'),
                'last_name' => $g('last_name'),
                'is_closed' => $g('is_closed'),
                'can_send_friend_request' => $g('can_send_friend_request'),
                'can_see_all_posts' => $g('can_see_all_posts'),
                'followers' => $g('counters.followers'),
                'friends_count' => $g('counters.friends'),
                'img' => $g('photo_max_orig')
            ];
        } else {

            $res = [
                'id' => $g('id'),
                'name' => $g('name'),
                'screen_name' => $g('screen_name'),
                'is_closed' => $g('is_closed'),
                'can_post' => $g('can_post'),
                'type' => $g('type'),
                'followers' => $g('members_count'),
                'img' => $g('photo_max_orig')
            ];
        }

        return $res;
    }

    public function profile($login)
    {
        //request limit
        $this->tooManyAttempts(__METHOD__);

        $typeOrRes = $this->isClub($login);
        if ($typeOrRes === 'user') {
            $type = 'user';
            $res = Http::get("{$this->host}users.get", [
                'user_ids' => $login,
                'access_token' => $this->key,
                'v' => $this->version,
                'fields' => 'bdate, can_send_friend_request, has_photo, blacklisted,can_see_all_posts,counters,photo_max_orig',
            ]);
            $parsed = $this->parseProfile($res->json('response'), $type);
        } else {
            $type = 'club';
            $parsed = $this->parseProfile($typeOrRes, $type);
        }

        return $parsed;

    }

    private function parseMedia($data, $type)
    {
        if(!isset($data)){
            throw new ScraperException('Bad response from vk api scraper');
        }

        $g = make_data_getter($data);

        switch ($type) {
            case 'wall':
                $res = [
                    'group_id' => $g('groups.0.id'),
                    'is_closed' => $g('groups.0.is_closed'),
                    'group_name' => $g('groups.0.name'),
                    'type' => $g('groups.0.type'),
                    'id' => $g('items.0.id'),
                    'comments' => $g('items.0.comments.count'),
                    'likes' => $g('items.0.likes.count'),
                    'reposts' => $g('items.0.reposts.count'),
                    'post_views' => $g('items.0.views.count'),
                    'first_name' => $g('profiles.0.first_name'),
                    'last_name' => $g('profiles.0.last_name'),
                    'is_closed' => $g('profiles.0.is_closed'),
                    'img' => $g('items.0.attachments.0.photo.sizes.3.url'),
                ];
                break;
            case 'video':
                $res = [
                    'group_id' => $g('groups.0.id'),
                    'is_closed' => $g('groups.0.is_closed'),
                    'group_name' => $g('groups.0.name'),
                    'type' => $g('groups.0.type'),
                    'first_name' => $g('profiles.0.first_name'),
                    'last_name' => $g('profiles.0.last_name'),
                    'title' => $g('items.0.title'),
                    'comments' => $g('items.0.comments'),
                    'likes' => $g('items.0.likes.count'),
                    'reposts' => $g('items.0.reposts.count'),
                    'views' => $g('items.0.views'),
                    'img' => $g('items.0.image.3.url'),
                ];
                break;
            case 'story':
                $res = [
                    'id' => $g('items.0.id'),
                    'comments' => $g('items.0.comments.count'),
                    'likes' => $g('items.0.likes.count'),
                    'reposts' => $g('items.0.reposts.count'),
                ];
                break;
            default:
                $res = [
                    'id' => $g('0.id'),
                    'post_id' => $g('0.post_id'),
                    'likes' => $g('0.likes.count'),
                    'reposts' => $g('0.reposts.count'),
                    'comments' => $g('0.comments.count'),
                    'img' => $g('0.sizes.3.url'),
                ];
        }
        return $res;
    }

    public function media($url)
    {
        //extended = 1 need for get info like a "can_comment" and other
        //but some method has another structure of response with this param
        $vkLinkType = [
            'wall' => ['method' => 'wall.getById', 'param' => 'posts', 'extended' => 1, 'type' => 'wall'],
            'video' => ['method' => 'video.get', 'param' => 'videos', 'extended' => 1, 'type' => 'video'],
            'photo' => ['method' => 'photos.getById', 'param' => 'photos', 'extended' => 1, 'type' => 'photo'],
            'story' => ['method' => 'stories.getById', 'param' => 'stories', 'extended' => 1, 'type' => 'story'],
        ];

        $pattern = "/(wall|video|photo|story)(\-)?([0-9._]+)/i";

        if (preg_match($pattern, $url, $matches) === 1) {
            [$full, $type, $isGroup, $id] = $matches;
            if ($isGroup !== null) {
                $id = $isGroup . $id;
            }

            $key = __METHOD__ . $type;
            $this->tooManyAttempts($key);

            $apiVkMethod = $vkLinkType[$type]['method'];
            $res = Http::get("{$this->host}/{$apiVkMethod}", [
                'access_token' => $this->key,
                'v' => $this->version,
                'extended' => $vkLinkType[$type]['extended'],
                $vkLinkType[$type]['param'] => $id
            ]);
            if ($res->successful() and empty($res->json('error.error_code'))) {
                $parsed = $this->parseMedia($res->json('response'), $vkLinkType[$type]['type']);
                return $parsed;
            } else {
                return 'some error';
            }
        }
    }

    public function feed($login, $first = 12)
    {
    }

    public function profileUser($login)
    {
        //request limit
        $this->tooManyAttempts(__METHOD__);

        $type = $this->isClub($login);
        if ($type !== 'user') {
            return 'Your link contains the club profile, but you need a link to the user profile';
        } else {
            $res = Http::get("{$this->host}users.get", [
                'user_ids' => $login,
                'access_token' => $this->key,
                'v' => $this->version,
                'extended' => 1,
                'fields' => 'bdate, can_send_friend_request, has_photo, can_see_all_posts, counters,photo_max_orig',
            ]);
        }
        if (empty($res->json('error.error_code')) and $res->successful()) {
            $parsed = $this->parseProfile($res->json('response'), $type);
            return $parsed;
        } else {
            return 'error' . $res->json('error.error_code');
        }
    }

    public function profileClub($login)
    {
        //request limit
        $this->tooManyAttempts(__METHOD__);

        $typeOrRes = $this->isClub($login);
        if ($typeOrRes == 'user') {
            return 'Your link contains the user profile, but you need a link to the club profile';
        } else {
            $type = 'club';
            $parsed = $this->parseProfile($typeOrRes, $type);
            return $parsed;
        }
    }

    private function isClub($login)
    {
        //request limit
        $this->tooManyAttempts(__METHOD__);

        $res = Http::get("{$this->host}groups.getById", [
            'group_id' => $login,
            'access_token' => $this->key,
            'v' => $this->version,
            'extended' => 1,
            'fields' => 'members_count,photo_max_orig',
        ]);
        if ($res->json('error.error_code') === 100 and $res->successful()) {
            return 'user';
        } elseif ($res->successful() and empty($res->json('error.error_code'))) {
            return $res->json('response');
        }
    }

    private function tooManyAttempts($key)
    {
        if(Cache::has($key)) {
            Cache::increment($key);
            if(Cache::get($key) >= 4) {
                throw new ScraperException('Too many attempts!');
            }
        } else {
            Cache::put($key, 1, 1);
        }
    }
}
