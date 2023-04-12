<?php

namespace App\Scraper\Simple;

use Illuminate\Support\Str;

class SimpleTiktokScraperFake implements ISimpleTiktokScraper
{
    public function profile($user)
    {
        $followers = random_int(100, 1000);
        $likes = random_int(1000, 10000);
        $posts = random_int(100, 1000);
        $login = 'tt_fake_login';
        $nickname = 'TTFakeNickname';
        $img = 'https://fake.jpg';
        $private = false;

        return compact('followers', 'likes',
            'posts','login','nickname',
            'img','private');
    }

    private function getRandomPost(): array
    {
        $id = random_int(999, 999999);
        $login = 'tt_fake_' . Str::random(8);
        $newPost = [
            'id' => $id,
            'login' => $login,
            'url' => 'https://www.tiktok.com/@'.$login.'/video/'.$id,
            'img' => 'http://fake.jpg',
            'likes' => random_int(10, 500),
            'reposts' => random_int(10, 500),
            'comments' => random_int(10, 500),
            'views' => random_int(10, 500),
        ];
        return $newPost;
    }

    public function feed($user) {
        $n = random_int(10, 50);
        $posts = [];
        for ($i = 1; $i <= $n; $i++) {
            $posts[] = $this->getRandomPost();
        }

        return $posts;
    }

    public function video($url)
    {
        return $this->getRandomPost();
    }
}
