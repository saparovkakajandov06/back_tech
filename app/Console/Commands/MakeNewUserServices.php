<?php

namespace App\Console\Commands;

use App\Domain\Splitters\DefaultSplitter;
use App\Domain\Transformers\General\SetRegion;
use App\Domain\Transformers\Instagram\ExtractCodeFromLink;
use App\Domain\Transformers\Instagram\ExtractLoginFromLink;
use App\Domain\Transformers\Parsers\ParseInstagramLink;
use App\Domain\Transformers\Parsers\ParseTelegramLink;
use App\Domain\Transformers\Parsers\ParseTelegramLogin;
use App\Domain\Transformers\Parsers\ParseVkLink;
use App\Domain\Transformers\SaveImg;
use App\Domain\Transformers\Scrapers\ScrapeIgMedia;
use App\Domain\Transformers\Scrapers\ScrapeTelegramProfile;
use App\Domain\Transformers\Scrapers\ScrapeTelegramViews;
use App\Domain\Transformers\Scrapers\ScrapeVkMedia;
use App\Domain\Transformers\Scrapers\SetRestylerIgScraper;
use App\Domain\Transformers\Scrapers\SetTelegramScraper;
use App\Domain\Transformers\Scrapers\SetVkApiScraper;
use App\Domain\Transformers\SetDefaultPriceFromCount;
use App\Domain\Transformers\SetImgFromUrl;
use App\Domain\Transformers\SetOneOrder;
use App\Domain\Validators\CheckHasLinkAndCount;
use App\Domain\Validators\CheckUserHasEnoughFunds;
use App\USPrice;
use Illuminate\Console\Command;
use US;

class MakeNewUserServices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smm:make_new_uss';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Added new user services 13.03.22';

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
        echo "Lets add user services \n\n";
        $uss = [
            US::INSTAGRAM_VIEWS_REELS_LK => [
                'title' => 'Просмотров reels',
                'tag' => US::INSTAGRAM_VIEWS_REELS_LK,
                'splitter' => DefaultSplitter::class,
                'config' => [
                    [
                        "max" => 1000000,
                        "min" => 100,
                        "name" => "",
                        "order" => '',
                        "net_cost" => [
                            "auto" => 0,
                            "mode" => "auto",
                            "local" => 0,
                            "amount" => 100,
                            "auto_timestamp" => "Fri Feb 18 2022 16:28:14 GMT+0300",
                        ],
                        "count_min" => "0",
                        "isEnabled" => false,
                        "remote_params" => [

                        ],
                        "service_class" => "",
                    ],
                ],
                'img' => "/svg/media-player.svg",
                'platform' => 'Instagram',
                'name' => 'Просмотры reels',
                'pipeline' => [
                    SetRegion::class, 
                    CheckHasLinkAndCount::class, 
                    ExtractCodeFromLink::class, 
                    ExtractLoginFromLink::class, 
                    ParseInstagramLink::class, 
                    SetRestylerIgScraper::class, 
                    ScrapeIgMedia::class, 
                    SetOneOrder::class, 
                    SetDefaultPriceFromCount::class, 
                    CheckUserHasEnoughFunds::class, 
                    SaveImg::class,
                ],
                'labels' => [
                    'TYPE_VIEWS', 
                    'DISCOUNT_VIEWS', 
                    'DISABLED', 
                    'CLIENT_LK', 
                    'VISIBLE'
                ],
                'min_order' => '100',
                'max_order' => '4000',
                'order_speed' => '1500',
                'order_frequency' => 'day',
            ],
            US::INSTAGRAM_VIEWS_REELS_MAIN => [
                'title' => 'Просмотров reels',
                'tag' => US::INSTAGRAM_VIEWS_REELS_MAIN,
                'splitter' => DefaultSplitter::class,
                'config' => [
                    [
                        "max" => 1000000,
                        "min" => 100,
                        "name" => "",
                        "order" => '',
                        "net_cost" => [
                            "auto" => 0,
                            "mode" => "auto",
                            "local" => 0,
                            "amount" => 100,
                            "auto_timestamp" => "Fri Feb 18 2022 16:28:14 GMT+0300",
                        ],
                        "count_min" => "0",
                        "isEnabled" => false,
                        "remote_params" => [

                        ],
                        "service_class" => "",
                    ],
                ],
                'img' => "/svg/media-player.svg",
                'platform' => 'Instagram',
                'name' => 'Просмотры reels',
                'pipeline' => [
                    SetRegion::class, 
                    CheckHasLinkAndCount::class, 
                    ExtractCodeFromLink::class, 
                    ExtractLoginFromLink::class, 
                    ParseInstagramLink::class, 
                    SetRestylerIgScraper::class, 
                    ScrapeIgMedia::class, 
                    SetOneOrder::class, 
                    SetDefaultPriceFromCount::class, 
                    SaveImg::class,
                ],
                'labels' => [
                    'TYPE_VIEWS', 
                    'DISCOUNT_VIEWS', 
                    'DISABLED', 
                    'CLIENT_MAIN', 
                    'VISIBLE'
                ],
                'min_order' => '100',
                'max_order' => '4000',
                'order_speed' => '1500',
                'order_frequency' => 'day',
            ],
            US::INSTAGRAM_VIEWS_IGTV_MAIN => [
                'title' => 'Просмотров IGTV',
                'tag' => US::INSTAGRAM_VIEWS_IGTV_MAIN,
                'splitter' => DefaultSplitter::class,
                'config' => [
                    [
                        "max" => 1000000,
                        "min" => 100,
                        "name" => "",
                        "order" => '',
                        "net_cost" => [
                            "auto" => 0,
                            "mode" => "auto",
                            "local" => 0,
                            "amount" => 100,
                            "auto_timestamp" => "Fri Feb 18 2022 16:28:14 GMT+0300",
                        ],
                        "count_min" => "0",
                        "isEnabled" => false,
                        "remote_params" => [

                        ],
                        "service_class" => "",
                    ],
                ],
                'img' => "/svg/media-player.svg",
                'platform' => 'Instagram',
                'name' => 'Просмотры IGTV',
                'pipeline' => [
                    SetRegion::class, 
                    CheckHasLinkAndCount::class, 
                    ExtractCodeFromLink::class, 
                    ExtractLoginFromLink::class, 
                    ParseInstagramLink::class, 
                    SetRestylerIgScraper::class, 
                    ScrapeIgMedia::class, 
                    SetOneOrder::class, 
                    SetDefaultPriceFromCount::class, 
                    SaveImg::class,
                ],
                'labels' => [
                    'TYPE_VIEWS', 
                    'DISCOUNT_VIEWS', 
                    'DISABLED', 
                    'CLIENT_MAIN', 
                    'VISIBLE'
                ],
                'min_order' => '100',
                'max_order' => '4000',
                'order_speed' => '1500',
                'order_frequency' => 'day',
            ],
            US::YOUTUBE_VIEWS_SHORTS_LK => [
                'title' => 'Просмотров shorts',
                'tag' => US::YOUTUBE_VIEWS_SHORTS_LK,
                'splitter' => DefaultSplitter::class,
                'config' => [
                    [
                        "max" => 1000000,
                        "min" => 100,
                        "name" => "",
                        "order" => '',
                        "net_cost" => [
                            "auto" => 0,
                            "mode" => "auto",
                            "local" => 0,
                            "amount" => 100,
                            "auto_timestamp" => "Fri Feb 18 2022 16:28:14 GMT+0300",
                        ],
                        "count_min" => "0",
                        "isEnabled" => false,
                        "remote_params" => [

                        ],
                        "service_class" => "",
                    ],
                ],
                'img' => "/svg/media-player.svg",
                'platform' => 'Youtube',
                'name' => 'Просмотры shorts',
                'pipeline' => [
                    SetRegion::class, 
                    CheckHasLinkAndCount::class, 
                    SetImgFromUrl::class, 
                    SetOneOrder::class, 
                    SetDefaultPriceFromCount::class, 
                    CheckUserHasEnoughFunds::class, 
                    SaveImg::class,
                ],
                'labels' => [
                    'TYPE_VIEWS', 
                    'DISCOUNT_VIEWS', 
                    'DISABLED', 
                    'CLIENT_LK', 
                    'VISIBLE'
                ],
                'min_order' => '100',
                'max_order' => '4000',
                'order_speed' => '1500',
                'order_frequency' => 'day',
            ],
            US::YOUTUBE_VIEWS_DURATION_LK => [
                'title' => 'Просмотров',
                'tag' => US::YOUTUBE_VIEWS_DURATION_LK,
                'splitter' => DefaultSplitter::class,
                'config' => [
                    [
                        "max" => 1000000,
                        "min" => 100,
                        "name" => "",
                        "order" => '',
                        "net_cost" => [
                            "auto" => 0,
                            "mode" => "auto",
                            "local" => 0,
                            "amount" => 100,
                            "auto_timestamp" => "Fri Feb 18 2022 16:28:14 GMT+0300",
                        ],
                        "count_min" => "0",
                        "isEnabled" => false,
                        "remote_params" => [

                        ],
                        "service_class" => "",
                    ],
                ],
                'img' => "/svg/media-player.svg",
                'platform' => 'Youtube',
                'name' => 'Часы просмотров Youtube',
                'pipeline' => [
                    SetRegion::class, 
                    CheckHasLinkAndCount::class, 
                    SetImgFromUrl::class, 
                    SetOneOrder::class, 
                    SetDefaultPriceFromCount::class, 
                    CheckUserHasEnoughFunds::class, 
                    SaveImg::class,
                ],
                'labels' => [
                    'TYPE_VIEWS', 
                    'DISCOUNT_VIEWS', 
                    'DISABLED', 
                    'CLIENT_LK', 
                    'VISIBLE'
                ],
                'min_order' => '100',
                'max_order' => '4000',
                'order_speed' => '1500',
                'order_frequency' => 'day',
            ],
            US::YOUTUBE_VIEWS_DURATION_MAIN => [
                'title' => 'Просмотров',
                'tag' => US::YOUTUBE_VIEWS_DURATION_MAIN,
                'splitter' => DefaultSplitter::class,
                'config' => [
                    [
                        "max" => 1000000,
                        "min" => 100,
                        "name" => "",
                        "order" => '',
                        "net_cost" => [
                            "auto" => 0,
                            "mode" => "auto",
                            "local" => 0,
                            "amount" => 100,
                            "auto_timestamp" => "Fri Feb 18 2022 16:28:14 GMT+0300",
                        ],
                        "count_min" => "0",
                        "isEnabled" => false,
                        "remote_params" => [

                        ],
                        "service_class" => "",
                    ],
                ],
                'img' => "/svg/media-player.svg",
                'platform' => 'Youtube',
                'name' => 'Часы просмотров Youtube',
                'pipeline' => [
                    SetRegion::class, 
                    CheckHasLinkAndCount::class, 
                    SetImgFromUrl::class, 
                    SetOneOrder::class, 
                    SetDefaultPriceFromCount::class, 
                    SaveImg::class,
                ],
                'labels' => [
                    'TYPE_VIEWS', 
                    'DISCOUNT_VIEWS', 
                    'DISABLED', 
                    'CLIENT_MAIN', 
                    'VISIBLE'
                ],
                'min_order' => '100',
                'max_order' => '4000',
                'order_speed' => '1500',
                'order_frequency' => 'day',
            ],
            US::TELEGRAM_VIEWS_LK => [
                'title' => 'Просмотров постов',
                'tag' => US::TELEGRAM_VIEWS_LK,
                'splitter' => DefaultSplitter::class,
                'config' => [
                    [
                        "max" => 1000000,
                        "min" => 100,
                        "name" => "",
                        "order" => '',
                        "net_cost" => [
                            "auto" => 0,
                            "mode" => "auto",
                            "local" => 0,
                            "amount" => 100,
                            "auto_timestamp" => "Fri Feb 18 2022 16:28:14 GMT+0300",
                        ],
                        "count_min" => "0",
                        "isEnabled" => false,
                        "remote_params" => [

                        ],
                        "service_class" => "",
                    ],
                ],
                'img' => "/svg/media-player.svg",
                'platform' => 'Telegram',
                'name' => 'Просмотры постов Telegram',
                'pipeline' => [
                    SetRegion::class, 
                    CheckHasLinkAndCount::class,
                    ParseTelegramLink::class,
                    SetTelegramScraper::class,
                    ScrapeTelegramViews::class, 
                    SetOneOrder::class, 
                    SetDefaultPriceFromCount::class,
                    CheckUserHasEnoughFunds::class, 
                ],
                'labels' => [
                    'TYPE_VIEWS', 
                    'DISCOUNT_VIEWS', 
                    'DISABLED', 
                    'CLIENT_LK', 
                    'VISIBLE'
                ],
                'min_order' => '100',
                'max_order' => '100000',
                'order_speed' => '1000',
                'order_frequency' => 'day',
            ],
            US::TELEGRAM_VIEWS_MAIN => [
                'title' => 'Просмотров постов',
                'tag' => US::TELEGRAM_VIEWS_MAIN,
                'splitter' => DefaultSplitter::class,
                'config' => [
                    [
                        "max" => 1000000,
                        "min" => 100,
                        "name" => "",
                        "order" => '',
                        "net_cost" => [
                            "auto" => 0,
                            "mode" => "auto",
                            "local" => 0,
                            "amount" => 100,
                            "auto_timestamp" => "Fri Feb 18 2022 16:28:14 GMT+0300",
                        ],
                        "count_min" => "0",
                        "isEnabled" => false,
                        "remote_params" => [

                        ],
                        "service_class" => "",
                    ],
                ],
                'img' => "/svg/media-player.svg",
                'platform' => 'Telegram',
                'name' => 'Просмотры постов Telegram',
                'pipeline' => [
                    SetRegion::class, 
                    CheckHasLinkAndCount::class,
                    ParseTelegramLink::class,
                    SetTelegramScraper::class,
                    ScrapeTelegramViews::class,   
                    SetOneOrder::class, 
                    SetDefaultPriceFromCount::class, 
                ],
                'labels' => [
                    'TYPE_VIEWS', 
                    'DISCOUNT_VIEWS', 
                    'DISABLED', 
                    'CLIENT_MAIN', 
                    'VISIBLE'
                ],
                'min_order' => '100',
                'max_order' => '100000',
                'order_speed' => '1000',
                'order_frequency' => 'day',
            ],
            US::TELEGRAM_SUBS_LK => [
                'title' => 'Подписчиков',
                'tag' => US::TELEGRAM_SUBS_LK,
                'splitter' => DefaultSplitter::class,
                'config' => [
                    [
                        "max" => 1000000,
                        "min" => 100,
                        "name" => "",
                        "order" => '',
                        "net_cost" => [
                            "auto" => 0,
                            "mode" => "auto",
                            "local" => 0,
                            "amount" => 100,
                            "auto_timestamp" => "Fri Feb 18 2022 16:28:14 GMT+0300",
                        ],
                        "count_min" => "0",
                        "isEnabled" => false,
                        "remote_params" => [

                        ],
                        "service_class" => "",
                    ],
                ],
                'img' => "/svg/subs.svg",
                'platform' => 'Telegram',
                'name' => 'Подписчики Telegram',
                'pipeline' => [
                    SetRegion::class, 
                    CheckHasLinkAndCount::class,
                    ParseTelegramLogin::class,
                    SetTelegramScraper::class,
                    ScrapeTelegramProfile::class, 
                    SetOneOrder::class, 
                    SetDefaultPriceFromCount::class,
                    CheckUserHasEnoughFunds::class, 
                ],
                'labels' => [
                    'TYPE_SUBS', 
                    'DISCOUNT_SUBS', 
                    'DISABLED', 
                    'CLIENT_LK', 
                    'VISIBLE'
                ],
                'min_order' => '100',
                'max_order' => '100000',
                'order_speed' => '1000',
                'order_frequency' => 'day',
            ],
            US::TELEGRAM_SUBS_MAIN => [
                'title' => 'Подписчиков',
                'tag' => US::TELEGRAM_SUBS_MAIN,
                'splitter' => DefaultSplitter::class,
                'config' => [
                    [
                        "max" => 1000000,
                        "min" => 100,
                        "name" => "",
                        "order" => '',
                        "net_cost" => [
                            "auto" => 0,
                            "mode" => "auto",
                            "local" => 0,
                            "amount" => 100,
                            "auto_timestamp" => "Fri Feb 18 2022 16:28:14 GMT+0300",
                        ],
                        "count_min" => "0",
                        "isEnabled" => false,
                        "remote_params" => [

                        ],
                        "service_class" => "",
                    ],
                ],
                'img' => "/svg/subs.svg",
                'platform' => 'Telegram',
                'name' => 'Подписчики Telegram',
                'pipeline' => [
                    SetRegion::class, 
                    CheckHasLinkAndCount::class,
                    ParseTelegramLogin::class,
                    SetTelegramScraper::class,
                    ScrapeTelegramProfile::class, 
                    SetOneOrder::class, 
                    SetDefaultPriceFromCount::class, 
                ],
                'labels' => [
                    'TYPE_SUBS', 
                    'DISCOUNT_SUBS', 
                    'DISABLED', 
                    'CLIENT_MAIN', 
                    'VISIBLE'
                ],
                'min_order' => '100',
                'max_order' => '100000',
                'order_speed' => '1000',
                'order_frequency' => 'day',
            ],
            US::VK_VIEWS_POST_MAIN => [
                'title' => 'Просмотров постов',
                'tag' => US::VK_VIEWS_POST_MAIN,
                'splitter' => DefaultSplitter::class,
                'config' => [
                    [
                        "max" => 1000000,
                        "min" => 100,
                        "name" => "",
                        "order" => '',
                        "net_cost" => [
                            "auto" => 0,
                            "mode" => "auto",
                            "local" => 0,
                            "amount" => 100,
                            "auto_timestamp" => "Fri Feb 18 2022 16:28:14 GMT+0300",
                        ],
                        "count_min" => "0",
                        "isEnabled" => false,
                        "remote_params" => [

                        ],
                        "service_class" => "",
                    ],
                ],
                'img' => "/svg/media-player.svg",
                'platform' => 'Vk',
                'name' => 'Просмотры постов Vk',
                'pipeline' => [
                    SetRegion::class, 
                    CheckHasLinkAndCount::class,
                    ParseVkLink::class, 
                    SetVkApiScraper::class,
                    ScrapeVkMedia::class,
                    SetOneOrder::class, 
                    SetDefaultPriceFromCount::class, 
                    SaveImg::class,
                ],
                'labels' => [
                    'TYPE_VIEWS', 
                    'DISCOUNT_VIEWS', 
                    'DISABLED', 
                    'CLIENT_MAIN', 
                    'VISIBLE'
                ],
                'min_order' => '100',
                'max_order' => '100000',
                'order_speed' => '1500',
                'order_frequency' => 'day',
            ],
            US::VK_VIEWS_POST_LK => [
                'title' => 'Просмотров постов',
                'tag' => US::VK_VIEWS_POST_LK,
                'splitter' => DefaultSplitter::class,
                'config' => [
                    [
                        "max" => 1000000,
                        "min" => 100,
                        "name" => "",
                        "order" => '',
                        "net_cost" => [
                            "auto" => 0,
                            "mode" => "auto",
                            "local" => 0,
                            "amount" => 100,
                            "auto_timestamp" => "Fri Feb 18 2022 16:28:14 GMT+0300",
                        ],
                        "count_min" => "0",
                        "isEnabled" => false,
                        "remote_params" => [

                        ],
                        "service_class" => "",
                    ],
                ],
                'img' => "/svg/media-player.svg",
                'platform' => 'Vk',
                'name' => 'Просмотры постов Vk',
                'pipeline' => [
                    SetRegion::class, 
                    CheckHasLinkAndCount::class,
                    ParseVkLink::class, 
                    SetVkApiScraper::class,
                    ScrapeVkMedia::class,
                    SetOneOrder::class, 
                    SetDefaultPriceFromCount::class,
                    CheckUserHasEnoughFunds::class, 
                    SaveImg::class,
                ],
                'labels' => [
                    'TYPE_VIEWS', 
                    'DISCOUNT_VIEWS', 
                    'DISABLED', 
                    'CLIENT_LK', 
                    'VISIBLE'
                ],
                'min_order' => '100',
                'max_order' => '100000',
                'order_speed' => '1500',
                'order_frequency' => 'day',
            ],
            US::VK_VIEWS_VIDEO_MAIN => [
                'title' => 'Просмотров',
                'tag' => US::VK_VIEWS_VIDEO_MAIN,
                'splitter' => DefaultSplitter::class,
                'config' => [
                    [
                        "max" => 1000000,
                        "min" => 100,
                        "name" => "",
                        "order" => '',
                        "net_cost" => [
                            "auto" => 0,
                            "mode" => "auto",
                            "local" => 0,
                            "amount" => 100,
                            "auto_timestamp" => "Fri Feb 18 2022 16:28:14 GMT+0300",
                        ],
                        "count_min" => "0",
                        "isEnabled" => false,
                        "remote_params" => [

                        ],
                        "service_class" => "",
                    ],
                ],
                'img' => "/svg/media-player.svg",
                'platform' => 'Vk',
                'name' => 'Просмотры видео Vk',
                'pipeline' => [
                    SetRegion::class, 
                    CheckHasLinkAndCount::class,
                    ParseVkLink::class, 
                    SetVkApiScraper::class,
                    ScrapeVkMedia::class,
                    SetOneOrder::class, 
                    SetDefaultPriceFromCount::class,
                    SaveImg::class,
                ],
                'labels' => [
                    'TYPE_VIEWS', 
                    'DISCOUNT_VIEWS', 
                    'DISABLED', 
                    'CLIENT_MAIN', 
                    'VISIBLE'
                ],
                'min_order' => '100',
                'max_order' => '100000',
                'order_speed' => '1500',
                'order_frequency' => 'day',
            ],
            US::VK_VIEWS_VIDEO_LK => [
                'title' => 'Просмотров',
                'tag' => US::VK_VIEWS_VIDEO_LK,
                'splitter' => DefaultSplitter::class,
                'config' => [
                    [
                        "max" => 1000000,
                        "min" => 100,
                        "name" => "",
                        "order" => '',
                        "net_cost" => [
                            "auto" => 0,
                            "mode" => "auto",
                            "local" => 0,
                            "amount" => 100,
                            "auto_timestamp" => "Fri Feb 18 2022 16:28:14 GMT+0300",
                        ],
                        "count_min" => "0",
                        "isEnabled" => false,
                        "remote_params" => [

                        ],
                        "service_class" => "",
                    ],
                ],
                'img' => "/svg/media-player.svg",
                'platform' => 'Vk',
                'name' => 'Просмотры видео Vk',
                'pipeline' => [
                    SetRegion::class, 
                    CheckHasLinkAndCount::class,
                    ParseVkLink::class, 
                    SetVkApiScraper::class,
                    ScrapeVkMedia::class,
                    SetOneOrder::class, 
                    SetDefaultPriceFromCount::class,
                    CheckUserHasEnoughFunds::class, 
                    SaveImg::class,
                ],
                'labels' => [
                    'TYPE_VIEWS', 
                    'DISCOUNT_VIEWS', 
                    'DISABLED', 
                    'CLIENT_LK', 
                    'VISIBLE'
                ],
                'min_order' => '100',
                'max_order' => '100000',
                'order_speed' => '1500',
                'order_frequency' => 'day',
            ]
        ];

        try{
            foreach($uss as $usKey => $us){
                US::create($us);
                $this->addPrices($usKey);
                $newUs = US::where('tag', $usKey)->first();
                echo $newUs?->id . "|" . $newUs?->tag . " done" . "\n";
            }
        } catch(\Throwable $e) {
            echo "error \n";
        }
        
        
        echo "All done \n";
    }

    private function addPrices($tag)
    {
        $arrPrices = [
            1 => 0,
            1000 => 0,
            3000 => 0,
            5000 => 0,
            10000 => 0,
            25000 => 0,
            50000 => 0
        ];

        if(!USPrice::where('tag', $tag)->first()){
            USPrice::create([
                'tag' => $tag,
                'EUR' => $arrPrices,
                'USD' => $arrPrices,
                'RUB' => $arrPrices,
                'TRY' => $arrPrices
            ]);

            echo "Prices for {$tag} created \n";
        } else {
            echo "Prices for {$tag} is already exists \n";
        }
        
    }
}
