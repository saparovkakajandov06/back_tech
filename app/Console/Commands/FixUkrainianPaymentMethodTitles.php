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

use App\PaymentMethod;
use Illuminate\Console\Command;
use US;

class FixUkrainianPaymentMethodTitles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smm:fix_ukrainian_payment_methods_title';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

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
        $methods = PaymentMethod::whereNotNull('titles->ua')->get();

        $methods->each(function ($method) {
            $titles = $method->titles;
            $titles['uk'] =  $titles['ua'];
            unset($titles['ua']);
            $method->update([
                 'titles' => $titles
             ]);
        });
    }
}
