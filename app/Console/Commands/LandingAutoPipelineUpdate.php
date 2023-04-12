<?php

namespace App\Console\Commands;

use App\Domain\Transformers\CopyCountToMinMax;
use App\Domain\Transformers\General\SetRegion;
use App\Domain\Transformers\Parsers\ParseInstagramLogin;
use App\Domain\Transformers\SaveImg;
use App\Domain\Transformers\Scrapers\ScrapeIgProfile;
use App\Domain\Transformers\Scrapers\SetRestylerIgScraper;
use App\Domain\Transformers\SetNormalAutoPrice;
use App\Domain\Transformers\SetOneOrder;
use App\Domain\Validators\CheckHasLoginCountPosts;
use Illuminate\Console\Command;
use US;

class LandingAutoPipelineUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smm:once_reconfigure_auto_main';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reconfigure Instagram landing auto services pipelines';

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
            US::INSTAGRAM_AUTO_VIEWS_MAIN => [
                SetRegion::class,
                CheckHasLoginCountPosts::class,
                CopyCountToMinMax::class,
                ParseInstagramLogin::class,
                SetRestylerIgScraper::class,
                ScrapeIgProfile::class,
                SetOneOrder::class,
                SetNormalAutoPrice::class,
                SaveImg::class,
            ],
            US::INSTAGRAM_AUTO_LIKES_MAIN => [
                SetRegion::class,
                CheckHasLoginCountPosts::class,
                CopyCountToMinMax::class,
                ParseInstagramLogin::class,
                SetRestylerIgScraper::class,
                ScrapeIgProfile::class,
                SetOneOrder::class,
                SetNormalAutoPrice::class,
                SaveImg::class,
            ],
        ];

        echo 'Make Instagram landing auto services great again!' . PHP_EOL;

        foreach ($data as $tag => $pipeline) {
            $s = US::where('tag', $tag)->firstOrFail();
            $s->update([
                'pipeline' => $pipeline,
            ]);
            echo $tag . PHP_EOL;
        }
        echo PHP_EOL . 'done' . PHP_EOL;
    }
}
