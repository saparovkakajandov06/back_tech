<?php

namespace App\Console\Commands;

use App\Domain\Transformers\General\SetRegion;
use App\Domain\Transformers\Parsers\ParseTiktokLink;
use App\Domain\Transformers\Parsers\ParseTiktokLogin;
use App\Domain\Transformers\Scrapers\ScrapeTiktokProfile;
use App\Domain\Transformers\Scrapers\ScrapeTiktokVideo;
use App\Domain\Transformers\Scrapers\SetKirtanTiktokScraper;
use App\Domain\Transformers\SetDefaultPriceFromCount;
use App\Domain\Transformers\SetNormalAutoPrice;
use App\Domain\Transformers\SetOneOrder;
use App\Domain\Validators\CheckHasLinkAndCount;
use App\Domain\Validators\CheckHasLoginAndCount;
use App\Domain\Validators\CheckHasLoginCountPosts;
use App\Domain\Validators\CheckUserHasEnoughFunds;
use Illuminate\Console\Command;
use US;

class MakeNewTiktok extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smm:make_new_tiktok';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reconfigure tiktok 06 june 2021';

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
            US::TIKTOK_LIKES_LK => [
                SetRegion::class,
                CheckHasLinkAndCount::class,
                ParseTiktokLink::class,
                SetKirtanTiktokScraper::class,
                ScrapeTiktokVideo::class,
                SetOneOrder::class,
                SetDefaultPriceFromCount::class,
                CheckUserHasEnoughFunds::class,
            ],
            US::TIKTOK_LIKES_MAIN => [
                SetRegion::class,
                CheckHasLinkAndCount::class,
                ParseTiktokLink::class,
                SetKirtanTiktokScraper::class,
                ScrapeTiktokVideo::class,
                SetOneOrder::class,
                SetDefaultPriceFromCount::class,
            ],
            US::TIKTOK_SUBS_LK => [
                SetRegion::class,
                CheckHasLoginAndCount::class,
                ParseTiktokLogin::class,
                SetKirtanTiktokScraper::class,
                ScrapeTiktokProfile::class,
                SetOneOrder::class,
                SetDefaultPriceFromCount::class,
                CheckUserHasEnoughFunds::class,
            ],
            US::TIKTOK_SUBS_MAIN => [
                SetRegion::class,
                CheckHasLoginAndCount::class,
                ParseTiktokLogin::class,
                SetKirtanTiktokScraper::class,
                ScrapeTiktokProfile::class,
                SetOneOrder::class,
                SetDefaultPriceFromCount::class,
            ],
            US::TIKTOK_AUTO_LIKES_LK => [
                SetRegion::class,
                CheckHasLoginCountPosts::class,
                ParseTiktokLogin::class,
                SetKirtanTiktokScraper::class,
                ScrapeTiktokProfile::class,
                SetOneOrder::class,
                SetNormalAutoPrice::class,
                CheckUserHasEnoughFunds::class,
            ],
            US::TIKTOK_AUTO_LIKES_MAIN => [
                SetRegion::class,
                CheckHasLoginCountPosts::class,
                ParseTiktokLogin::class,
                SetKirtanTiktokScraper::class,
                ScrapeTiktokProfile::class,
                SetOneOrder::class,
                SetNormalAutoPrice::class,
            ],
            US::TIKTOK_VIEWS_LK => [
                SetRegion::class,
                CheckHasLinkAndCount::class,
                ParseTiktokLink::class,
                SetKirtanTiktokScraper::class,
                ScrapeTiktokVideo::class,
                SetOneOrder::class,
                SetDefaultPriceFromCount::class,
                CheckUserHasEnoughFunds::class,
            ],
            US::TIKTOK_VIEWS_MAIN => [
                SetRegion::class,
                CheckHasLinkAndCount::class,
                ParseTiktokLink::class,
                SetKirtanTiktokScraper::class,
                ScrapeTiktokVideo::class,
                SetOneOrder::class,
                SetDefaultPriceFromCount::class,
            ],
            US::TIKTOK_AUTO_VIEWS_LK => [
                SetRegion::class,
                CheckHasLoginCountPosts::class,
                ParseTiktokLogin::class,
                SetKirtanTiktokScraper::class,
                ScrapeTiktokProfile::class,
                SetOneOrder::class,
                SetNormalAutoPrice::class,
                CheckUserHasEnoughFunds::class,
            ],
            US::TIKTOK_AUTO_VIEWS_MAIN => [
                SetRegion::class,
                CheckHasLoginCountPosts::class,
                ParseTiktokLogin::class,
                SetKirtanTiktokScraper::class,
                ScrapeTiktokProfile::class,
                SetOneOrder::class,
                SetNormalAutoPrice::class,
            ],
            US::TIKTOK_REPOSTS_LK => [
                SetRegion::class,
                CheckHasLinkAndCount::class,
                ParseTiktokLink::class,
                SetKirtanTiktokScraper::class,
                ScrapeTiktokVideo::class,
                SetOneOrder::class,
                SetDefaultPriceFromCount::class,
                CheckUserHasEnoughFunds::class,
            ],
            US::TIKTOK_REPOSTS_MAIN => [
                SetRegion::class,
                CheckHasLinkAndCount::class,
                ParseTiktokLink::class,
                SetKirtanTiktokScraper::class,
                ScrapeTiktokVideo::class,
                SetOneOrder::class,
                SetDefaultPriceFromCount::class,
            ],
            US::TIKTOK_COMMENTS_CUSTOM_LK => [
                SetRegion::class,
                CheckHasLinkAndCount::class, // + comments array
                ParseTiktokLink::class,
                SetKirtanTiktokScraper::class,
                ScrapeTiktokVideo::class,
                SetOneOrder::class,
                SetDefaultPriceFromCount::class,
                CheckUserHasEnoughFunds::class,
            ],
            US::TIKTOK_COMMENTS_POSITIVE_LK => [
                SetRegion::class,
                CheckHasLinkAndCount::class,
                ParseTiktokLink::class,
                SetKirtanTiktokScraper::class,
                ScrapeTiktokVideo::class,
                SetOneOrder::class,
                SetDefaultPriceFromCount::class,
                CheckUserHasEnoughFunds::class,
            ],
        ];

        echo "Make Tiktok great again!\n";

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
