<?php

namespace App\Console\Commands;

use App\Domain\Transformers\General\SetRegion;
use App\Domain\Transformers\Instagram\ExtractStoryLinkFromLogin;
use App\Domain\Transformers\SaveImg;
use App\Domain\Transformers\Scrapers\ScrapeIgProfile;
use App\Domain\Transformers\Scrapers\SetRestylerIgScraper;
use App\Domain\Transformers\SetDefaultPriceFromCount;
use App\Domain\Transformers\SetOneOrder;
use App\Domain\Validators\CheckHasLoginAndCount;
use App\Domain\Validators\CheckUserHasEnoughFunds;
use Illuminate\Console\Command;
use US;

class StoriesPipelineUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smm:once_reconfigure_stories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reconfigure Instagram stories pipeline';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $data = [
            US::INSTAGRAM_VIEWS_STORY_LK => [
                SetRegion::class,
                CheckHasLoginAndCount::class,
                ExtractStoryLinkFromLogin::class,
                SetRestylerIgScraper::class,
                ScrapeIgProfile::class,
                SetOneOrder::class,
                SetDefaultPriceFromCount::class,
                CheckUserHasEnoughFunds::class,
                SaveImg::class,
            ],
            US::INSTAGRAM_VIEWS_STORY_MAIN => [
                SetRegion::class,
                CheckHasLoginAndCount::class,
                ExtractStoryLinkFromLogin::class,
                SetRestylerIgScraper::class,
                ScrapeIgProfile::class,
                SetOneOrder::class,
                SetDefaultPriceFromCount::class,
                SaveImg::class,
            ],
        ];

        echo "Make Instagram stories great again!\n";

        foreach ($data as $tag => $pipeline) {
            $s = US::where('tag', $tag)->firstOrFail();
            $s->update([
                'pipeline' => $pipeline,
            ]);
            echo "$tag\n";
        }
        echo "\ndone\n";
    }
}
