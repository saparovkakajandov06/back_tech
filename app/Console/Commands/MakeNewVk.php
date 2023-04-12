<?php

namespace App\Console\Commands;

use App\Domain\Transformers\CopyLoginToLink;
use App\Domain\Transformers\General\SetRegion;
use App\Domain\Transformers\Parsers\ParseVkLink;
use App\Domain\Transformers\Parsers\ParseVkLogin;
use App\Domain\Transformers\SaveImg;
use App\Domain\Transformers\Scrapers\ScrapeVkMedia;
use App\Domain\Transformers\Scrapers\ScrapeVkProfile;
use App\Domain\Transformers\Scrapers\ScrapeVkUserProfile;
use App\Domain\Transformers\Scrapers\ScrapeVkClubProfile;
use App\Domain\Transformers\Scrapers\SetVkApiScraper;
use App\Domain\Transformers\SetDefaultPriceFromCount;
use App\Domain\Transformers\SetNormalAutoPrice;
use App\Domain\Transformers\SetOneOrder;
use App\Domain\Validators\CheckHasLoginCountPosts;
use App\Domain\Validators\CheckUserHasEnoughFunds;
use Illuminate\Console\Command;
use US;

class MakeNewVk extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smm:make_new_vk';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reconfigure VK 13 september 2021';

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
            US::VK_LIKES_LK => [
                SetRegion::class,
                ParseVkLink::class,
                SetVkApiScraper::class,
                ScrapeVkMedia::class,
                SetOneOrder::class,
                SetDefaultPriceFromCount::class,
                CheckUserHasEnoughFunds::class,
                SaveImg::class,
            ],
            US::VK_LIKES_MAIN => [
                SetRegion::class,
                ParseVkLink::class,
                SetVkApiScraper::class,
                ScrapeVkMedia::class,
                SetOneOrder::class,
                SetDefaultPriceFromCount::class,
                SaveImg::class,
            ],
            US::VK_FRIENDS_LK => [
                SetRegion::class,
                ParseVkLogin::class,
                SetVkApiScraper::class,
                ScrapeVkUserProfile::class,
                SetOneOrder::class,
                SetDefaultPriceFromCount::class,
                CheckUserHasEnoughFunds::class,
                SaveImg::class,
            ],
            US::VK_COMMENTS_LK => [
                SetRegion::class,
                ParseVkLink::class,
                SetVkApiScraper::class,
                ScrapeVkMedia::class,
                SetOneOrder::class,
                SetDefaultPriceFromCount::class,
                CheckUserHasEnoughFunds::class,
                SaveImg::class,
            ],
            US::VK_REPOSTS_MAIN => [
                SetRegion::class,
                ParseVkLink::class,
                SetVkApiScraper::class,
                ScrapeVkMedia::class,
                SetOneOrder::class,
                SetDefaultPriceFromCount::class,
                SaveImg::class,
            ],
            US::VK_SUBS_MAIN => [
                SetRegion::class,
                ParseVkLogin::class,
                SetVkApiScraper::class,
                ScrapeVkClubProfile::class,
                SetOneOrder::class,
                SetDefaultPriceFromCount::class,
                SaveImg::class,
            ],
            US::VK_AUTO_LIKES_MAIN => [
                SetRegion::class,
                CheckHasLoginCountPosts::class,
                ParseVkLogin::class,
                CopyLoginToLink::class,
                SetVkApiScraper::class,
                ScrapeVkProfile::class,
                SetOneOrder::class,
                SetDefaultPriceFromCount::class,
                SetNormalAutoPrice::class,
                SaveImg::class,
            ],
            US::VK_REPOSTS_LK => [
                SetRegion::class,
                ParseVkLink::class,
                SetVkApiScraper::class,
                ScrapeVkMedia::class,
                SetOneOrder::class,
                SetDefaultPriceFromCount::class,
                CheckUserHasEnoughFunds::class,
                SaveImg::class,
            ],
            US::VK_AUTO_LIKES_LK => [
                SetRegion::class,
                CheckHasLoginCountPosts::class,
                ParseVkLogin::class,
                CopyLoginToLink::class,
                SetVkApiScraper::class,
                ScrapeVkProfile::class,
                SetOneOrder::class,
                SetDefaultPriceFromCount::class,
                SetNormalAutoPrice::class,
                CheckUserHasEnoughFunds::class,
                SaveImg::class,
            ],
            US::VK_SUBS_LK => [
                SetRegion::class,
                ParseVkLogin::class,
                SetVkApiScraper::class,
                ScrapeVkClubProfile::class,
                SetOneOrder::class,
                SetDefaultPriceFromCount::class,
                CheckUserHasEnoughFunds::class,
                SaveImg::class,
            ],
            US::VK_FRIENDS_MAIN => [
                SetRegion::class,
                ParseVkLogin::class,
                SetVkApiScraper::class,
                ScrapeVkUserProfile::class,
                SetOneOrder::class,
                SetDefaultPriceFromCount::class,
                SaveImg::class,
            ],
            US::VK_COMMENTS_MAIN => [
                SetRegion::class,
                ParseVkLink::class,
                SetVkApiScraper::class,
                ScrapeVkMedia::class,
                SetOneOrder::class,
                SetDefaultPriceFromCount::class,
                SaveImg::class,
            ],
        ];

        echo "Make VK great again!\n";

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
