<?php

namespace App\Console\Commands;

use App\Domain\Transformers\Instagram\ExtractCodeFromLink;
use Illuminate\Console\Command;
use US;

class AddIgHashExtractor extends Command
{
    protected $signature = 'configure:new_hash_extractor';

    public $tags = [
        US::INSTAGRAM_LIKES_LK,
        US::INSTAGRAM_VIEWS_VIDEO_LK,
        US::INSTAGRAM_VIEWS_IGTV_LK,
        US::INSTAGRAM_VIEWS_SHOWS_IMPRESSIONS_LK,
        US::INSTAGRAM_COMMENTS_POSITIVE_LK,
        US::INSTAGRAM_COMMENTS_CUSTOM_LK,
        US::INSTAGRAM_LIKES_MAIN,
        US::INSTAGRAM_VIEWS_VIDEO_MAIN
    ];

    public function updatePipelines()
    {
        foreach($this->tags as $tag) {
            $service = US::where('tag', $tag)->firstOrFail();
            if (in_array(ExtractCodeFromLink::class, $service->pipeline)) {
                continue;
            }
            $pipeline = $service->pipeline;
            array_splice($pipeline, 2, 0, [ExtractCodeFromLink::class]);
            $service->pipeline = $pipeline;
            $service->save();
        }
    }

    public function handle()
    {
        $this->updatePipelines();
        echo PHP_EOL . "--- done ---" . PHP_EOL;
    }
}
