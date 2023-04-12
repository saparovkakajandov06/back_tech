<?php

namespace App\Console\Commands;

use App\Domain\Transformers\General\SetRegion;
use App\Domain\Transformers\Parsers\ParseInstagramLogin;
use App\Domain\Transformers\SaveImg;
use App\Domain\Transformers\Scrapers\ScrapeIgProfile;
use App\Domain\Transformers\Scrapers\SetRestylerIgScraper;
use App\Domain\Transformers\SetDefaultPriceFromCount;
use App\Domain\Transformers\SetOneOrder;
use App\Domain\Validators\CheckHasLoginAndCount;
use App\Domain\Validators\CheckUserHasEnoughFunds;
use Illuminate\Console\Command;
use US;

class LKLivePipelineUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smm:once_reconfigure_live_lk';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reconfigure Instagram lk live services pipelines';

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
            US::INSTAGRAM_LIVE_LIKES_LK => [
                SetRegion::class,
                CheckHasLoginAndCount::class,
                ParseInstagramLogin::class,
                SetRestylerIgScraper::class,
                ScrapeIgProfile::class,
                SetOneOrder::class,
                SetDefaultPriceFromCount::class,
                CheckUserHasEnoughFunds::class,
                SaveImg::class,
            ],
            US::INSTAGRAM_LIVE_VIEWERS_LK => [
                SetRegion::class,
                CheckHasLoginAndCount::class,
                ParseInstagramLogin::class,
                SetRestylerIgScraper::class,
                ScrapeIgProfile::class,
                SetOneOrder::class,
                SetDefaultPriceFromCount::class,
                CheckUserHasEnoughFunds::class,
                SaveImg::class,
            ],
        ];

        echo 'Make Instagram lk live services great again!' . PHP_EOL;

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
