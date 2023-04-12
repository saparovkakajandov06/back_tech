<?php

namespace App\Console\Commands;

use App\USPrice;
use Illuminate\Console\Command;

class RenameTagsInPrices extends Command
{
    protected $signature = 'smm:rename_tags_in_prices';

    protected $description = 'Rename tags in prices 26 july 2021';

    protected $data = [
        'INSTAGRAM_LIKES_LIVE_LK' => 'INSTAGRAM_LIVE_LIKES_LK',
        'INSTAGRAM_VIEWS_VIDEO_IMPRESSIONS_LK' => 'INSTAGRAM_VIEWS_VIDEO_LK',
        'INSTAGRAM_VIEWS_SHOW_IMPRESSIONS_LK' => 'INSTAGRAM_VIEWS_SHOWS_IMPRESSIONS_LK',
        'INSTAGRAM_VIEWS_VIEWERS_LIVE_LK' => 'INSTAGRAM_LIVE_VIEWERS_LK',

        'TIKTOK_VIEW_MAIN' => 'TIKTOK_VIEWS_MAIN',
        'TIKTOK_AUTOVIEW_MAIN' => 'TIKTOK_AUTO_VIEWS_MAIN',
        'TIKTOK_AUTOLIKE_MAIN' => 'TIKTOK_AUTO_LIKES_MAIN',
        'TIKTOK_REPOSTS_STORY_MAIN' => 'TIKTOK_REPOSTS_MAIN',
        'TIKTOK_VIEW_LK' => 'TIKTOK_VIEWS_LK',
        'TIKTOK_AUTOLIKE_LK' => 'TIKTOK_AUTO_LIKES_LK',

        'VK_FRIENDS_VIEW_MAIN' => 'VK_FRIENDS_MAIN',
        'VK_COMMENTS_AUTOVIEW_MAIN' => 'VK_COMMENTS_MAIN',
        'VK_AUTOVLIKE_MAIN' => 'VK_AUTO_LIKES_MAIN',
        'VK_REPOSTS_STORY_MAIN' => 'VK_REPOSTS_MAIN',
        'VK_FRIENDS_VIEW_LK' => 'VK_FRIENDS_LK',
        'VK_COMMENTS_AUTOVIEW_LK' => 'VK_COMMENTS_LK',
        'VK_AUTOVLIKE_LK' => 'VK_AUTO_LIKES_LK',
        'VK_REPOSTS_STORY_LK' => 'VK_REPOSTS_LK',
        'TIKTOK_AUTOVIEW_LK' => 'TIKTOK_AUTO_VIEWS_LK',
    ];

    public function handle()
    {
        $renamed = $left = 0;
        foreach(USPrice::all() as $price) {
            if ($newTag = $this->data[$price->tag] ?? null) {
                echo "update {$price->tag} -> $newTag\n";
                $price->update([ 'tag' => $newTag ]);
                $renamed++;
            } else {
                $left++;
            }
        }
        echo "done. renamed $renamed left unchanged $left\n";
    }
}
