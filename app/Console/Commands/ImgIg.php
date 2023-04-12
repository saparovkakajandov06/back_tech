<?php

namespace App\Console\Commands;

use App\Domain\Transformers\SaveImg;
use Illuminate\Console\Command;
use US;

class ImgIg extends Command
{
    protected $signature = 'smm:img_ig';

    protected $description = 'Save img 06 aug 2021';

//"INSTAGRAM_LIKES_LK",
//"INSTAGRAM_SUBS_LK",
//"INSTAGRAM_COMMENTS_CUSTOM_LK",
//"INSTAGRAM_COMMENTS_POSITIVE_LK",
//"INSTAGRAM_AUTO_LIKES_LK",
//"INSTAGRAM_AUTO_VIEWS_LK",
//"INSTAGRAM_LIVE_LIKES_LK",
//"INSTAGRAM_LIVE_VIEWERS_LK",
//"INSTAGRAM_VIEWS_IGTV_LK",
//"INSTAGRAM_VIEWS_STORY_LK",
//"INSTAGRAM_VIEWS_SHOWS_IMPRESSIONS_LK",
//"INSTAGRAM_VIEWS_VIDEO_LK",
//"INSTAGRAM_MULTI_LIKES_LK",
//"INSTAGRAM_MULTI_COMMENTS_CUSTOM_LK",
//"INSTAGRAM_MULTI_COMMENTS_POSITIVE_LK",
//"INSTAGRAM_AUTO_LIKES_MAIN",
//"INSTAGRAM_AUTO_VIEWS_MAIN",
//"INSTAGRAM_LIKES_MAIN",
//"INSTAGRAM_MULTI_LIKES_MAIN",
//"INSTAGRAM_SUBS_MAIN",
//"INSTAGRAM_VIEWS_STORY_MAIN",
//"INSTAGRAM_VIEWS_VIDEO_MAIN",

    public function handle()
    {
        echo "add SaveImg::class\n";

        US::get()->each(function($us) {
            $pipeline = $us->pipeline;
            $pipeline[] = SaveImg::class;
            $us->update([ 'pipeline' => $pipeline ]);

            echo "{$us->tag}\n";
        });

        echo "\ndone\n";
    }
}
