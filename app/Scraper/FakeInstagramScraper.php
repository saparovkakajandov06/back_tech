<?php /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */

/** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */

namespace App\Scraper;

use App\Proxy;

class FakeInstagramScraper implements InstagramScraper
{
    public function getMedia($link)
    {
        return [
            "img" => "https://fake",
            "profileName" => "",
            "profilePhoto" => "https://fake",
            "countLike" => 0,
            "countViews" => 0,
            "countComment" => 0,
            "error" => null,
            "status" => "ok",
            "link" => $link,
        ];
    }

    public function getPosts($login, $count)
    {
        return [];
    }

    public function healthCheck(): bool
    {
        return true;
    }

    public function getUser($login)
    {
        return [
            'login' => $login,
            'link' => "https://instagram.com/$login",
            'profilePhoto' => 'https://fake.photo',
            'followersCount' => 0,
            'fake' => true,
        ];
    }
}
