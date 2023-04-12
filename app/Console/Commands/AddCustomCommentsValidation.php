<?php

namespace App\Console\Commands;

use App\Domain\Transformers\General\SetRegion;
use App\Domain\Transformers\Parsers\ParseTiktokLink;
use App\Domain\Transformers\Scrapers\ScrapeTiktokVideo;
use App\Domain\Transformers\Scrapers\SetKirtanTiktokScraper;
use App\Domain\Transformers\Parsers\ParseInstagramLink;
use App\Domain\Transformers\Scrapers\SetRestylerIgScraper;
use App\Domain\Transformers\Scrapers\ScrapeIgMedia;
use App\Domain\Transformers\SetDefaultPriceFromCount;
use App\Domain\Transformers\SetOneOrder;
use App\Domain\Validators\CheckHasLinkAndCount;
use App\Domain\Validators\CheckUserHasEnoughFunds;
use App\Domain\Validators\CheckHasCustomComments;
use App\Domain\Transformers\SaveImg;

use Illuminate\Console\Command;
use US;

class AddCustomCommentsValidation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smm:add_custom_comments_validation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add validation to user services with custom comments  5 october 2021';

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
            US::TIKTOK_COMMENTS_CUSTOM_LK => [
                SetRegion::class,
                CheckHasLinkAndCount::class, 
                CheckHasCustomComments::class,
                ParseTiktokLink::class,
                SetKirtanTiktokScraper::class,
                ScrapeTiktokVideo::class,
                SetOneOrder::class,
                SetDefaultPriceFromCount::class,
                CheckUserHasEnoughFunds::class,
                SaveImg::class,
            ],
            US::INSTAGRAM_COMMENTS_CUSTOM_LK => [
                SetRegion::class,
                CheckHasLinkAndCount::class,
                CheckHasCustomComments::class,
                ParseInstagramLink::class,
                SetRestylerIgScraper::class,
                ScrapeIgMedia::class,
                SetOneOrder::class,
                SetDefaultPriceFromCount::class,
                CheckUserHasEnoughFunds::class,
                SaveImg::class
            ],
        ];

        echo "Make custom comments great again!\n";

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
