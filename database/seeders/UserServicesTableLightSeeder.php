<?php

namespace Database\Seeders;

use App\Domain\Models\Slots;
use App\Domain\Services\AbstractService;
use App\Domain\Services\Everve\AEverve;
use App\Domain\Services\Nakrutka\ANakrutka;
use App\Domain\Services\NakrutkaAuto\ANakrutkaAuto;
use App\Domain\Services\Socgress\ASocgress;
use App\Domain\Services\Vkserfing\AVkserfing;
use App\Domain\Services\VkserfingAuto\AVkserfingAuto;
use App\Domain\Services\Vtope\AVtope;
use App\Domain\Splitters\DefaultSplitter;
use App\Domain\Splitters\NakrutkaOneAutoChunk;
use App\Domain\Splitters\VkSerfSplitter;
use App\Domain\Transformers\CopyLoginToLink;
use App\Domain\Transformers\General\SetRegion;
use App\Domain\Transformers\Instagram\ExtractCodeFromLink;
use App\Domain\Transformers\Instagram\RealSetImgFromLogin;
use App\Domain\Transformers\Instagram\SetImgFromLinkAsMediaUrl;
use App\Domain\Transformers\Instagram\SetImgFromLogin;
use App\Domain\Transformers\Instagram\SetLinkAsFullUrlWithLogin;
use App\Domain\Transformers\Instagram\SetLoginFromLink;
use App\Domain\Transformers\Instagram\SetLoginFromLinkAsMediaUrl;
use App\Domain\Transformers\Instagram\SetMultiOrders;
use App\Domain\Transformers\Parsers\ParseTiktokLink;
use App\Domain\Transformers\Parsers\ParseTiktokLogin;
use App\Domain\Transformers\Scrapers\ScrapeIgMedia;
use App\Domain\Transformers\Scrapers\ScrapeIgProfile;
use App\Domain\Transformers\Scrapers\SetRestylerIgScraper;
use App\Domain\Transformers\SetAutoPrice;
use App\Domain\Transformers\SetCountFromMinMaxPosts;
use App\Domain\Transformers\SetDefaultPriceFromCount;
use App\Domain\Transformers\SetImgFromUrl;
use App\Domain\Transformers\SetNormalAutoPrice;
use App\Domain\Transformers\SetOneOrder;
use App\Domain\Validators\CheckHasLinkAndCount;
use App\Domain\Validators\CheckHasLoginAndCount;
use App\Domain\Validators\CheckHasLoginCountPosts;
use App\Domain\Validators\CheckHasLoginMinMaxPosts;
use App\Domain\Validators\CheckHasTargets;
use App\Domain\Validators\CheckLinkAsMediaUrl;
use App\Domain\Validators\CheckUserHasEnoughFunds;
use App\Domain\Validators\CheckUserHasEnoughPosts;
use App\Domain\Validators\RealCheckUserHasEnoughPosts;
use App\UserService;
use Illuminate\Database\Seeder;


class UserServicesTableLightSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $arrayOfServices = array(
            array (
                'id' => 116,
                'title' => 'Лайков',
                'tag' => UserService::INSTAGRAM_LIKES_LIGHT4,
                'splitter' => 'App\\Domain\\Splitters\\DefaultSplitter',
                'config' => [
                    [
                        'name' => Slots::INSTAGRAM_LIKES_MAIN_VKSERFING,
                        'service_class' => AVkserfing::class,
                        'order' => 1,
                        'min' => 5,
                        'max' => 90,
                        'remote_params' => [
                            'type' => 'instagram_like',
                        ],
                        'net_cost' => [
                            'amount' => 100,
                            'local' => 5,
                            'mode' => AbstractService::NET_COST_LOCAL,
                            'auto' => 0,
                            'auto_timestamp' => null,
                        ],
                        'isEnabled' => false,
                        'count_extra_percent' => 10,
                    ],
                    [
                        'name' => Slots::INSTAGRAM_LIKES_MAIN_EVERVE,
                        'service_class' => AEverve::class,
                        'order' => 2,
                        'min' => 5,
                        'max' => 100,
                        'remote_params' => [
                            'category_id' => 18,
                            'order_price' => 0.001,
                        ],
                        'net_cost' => [
                            'amount' => 100,
                            'local' => 7.1,
                            'mode' => AbstractService::NET_COST_LOCAL,
                            'auto' => 0,
                            'auto_timestamp' => null,
                        ],
                        'isEnabled' => false,
                        'count_extra_percent' => 0,
                    ],
                    [
                        'name' => Slots::INSTAGRAM_LIKES_MAIN_SOCGRESS,
                        'service_class' => ASocgress::class,
                        'order' => 3,
                        'min' => 5,
                        'max' => 300,
                        'remote_params' => [
                            'service_id' => 33,
                            'network' => 'instagram',
                            'speed' => 'slow',
                        ],
                        'net_cost' => [
                            'amount' => 100,
                            'local' => 7,
                            'mode' => AbstractService::NET_COST_LOCAL,
                            'auto' => 0,
                            'auto_timestamp' => null,
                        ],
                        'isEnabled' => false,
                        'count_extra_percent' => 10,
                    ],
                    [
                        'name' => Slots::INSTAGRAM_LIKES_MAIN_NAKRUTKA,
                        'service_class' => ANakrutka::class,
                        'order' => 4,
                        'min' => 50,
                        'max' => 10000,
                        'remote_params' => [
                            'service' => 1,
                        ],
                        'net_cost' => [
                            'amount' => 100,
                            'local' => 4.187,
                            'mode' => AbstractService::NET_COST_LOCAL,
                            'auto' => 0,
                            'auto_timestamp' => null,
                        ],
                        'isEnabled' => true,
                        'count_extra_percent' => 10,
                    ],
                    [
                        'name' => Slots::INSTAGRAM_LIKES_MAIN_NAKRUTKA_2,
                        'service_class' => ANakrutka::class,
                        'order' => 5,
                        'min' => 50,
                        'max' => 15000,
                        'remote_params' => [
                            'service' => 80,
                        ],
                        'net_cost' => [
                            'amount' => 100,
                            'local' => 4.187,
                            'mode' => AbstractService::NET_COST_LOCAL,
                            'auto' => 0,
                            'auto_timestamp' => null,
                        ],
                        'isEnabled' => true,
                        'count_extra_percent' => 10,
                    ],
                    [
                        'name' => Slots::INSTAGRAM_LIKES_MAIN_NAKRUTKA_3,
                        'service_class' => ANakrutka::class,
                        'order' => 6,
                        'min' => 50,
                        'max' => 10000,
                        'remote_params' => [
                            'service' => 11,
                        ],
                        'net_cost' => [
                            'amount' => 100,
                            'local' => 4.187,
                            'mode' => AbstractService::NET_COST_LOCAL,
                            'auto' => 0,
                            'auto_timestamp' => null,
                        ],
                        'isEnabled' => true,
                        'count_extra_percent' => 0,
                    ],
                    [
                        'name' => Slots::INSTAGRAM_LIKES_MAIN_NAKRUTKA_4,
                        'service_class' => ANakrutka::class,
                        'order' => 7,
                        'min' => 50,
                        'max' => 5000,
                        'remote_params' => [
                            'service' => 79,
                        ],
                        'net_cost' => [
                            'amount' => 100,
                            'local' => 4.187,
                            'mode' => AbstractService::NET_COST_LOCAL,
                            'auto' => 0,
                            'auto_timestamp' => null,
                        ],
                        'isEnabled' => true,
                        'count_extra_percent' => 10,
                    ],
                    [
                        'name' => Slots::INSTAGRAM_LIKES_MAIN_NAKRUTKA_5,
                        'service_class' => ANakrutka::class,
                        'order' => 8,
                        'min' => 50,
                        'max' => 1000_000,
                        'remote_params' => [
                            'service' => 20,
                        ],
                        'net_cost' => [
                            'amount' => 100,
                            'local' => 4.187,
                            'mode' => AbstractService::NET_COST_LOCAL,
                            'auto' => 0,
                            'auto_timestamp' => null,
                        ],
                        'isEnabled' => true,
                        'count_extra_percent' => 10,
                    ],
                ],
                'img' => '/svg/media-player.svg',
                'created_at' => '2020-07-02 23:33:47+03',
                'updated_at' => '2021-06-07 22:33:59+03',
                'description' => NULL,
                'card' => '["\\u0432\\u044b\\u0441\\u043e\\u043a\\u043e\\u0435 \\u043a\\u0430\\u0447\\u0435\\u0441\\u0442\\u0432\\u043e","\\u043c\\u0433\\u043d\\u043e\\u0432\\u0435\\u043d\\u043d\\u044b\\u0439 \\u0437\\u0430\\u043f\\u0443\\u0441\\u043a","\\u0441 \\u0430\\u0432\\u0430\\u0442\\u0430\\u0440\\u043a\\u0430\\u043c\\u0438","\\u043d\\u0435\\u0442 \\u0431\\u0430\\u043d\\u0430","\\u0434\\u043e 10 \\u0442\\u044b\\u0441. \\u0432 \\u0441\\u0443\\u0442\\u043a\\u0438"]',
                'local_validation' => NULL,
                'local_checker' => NULL,
                'tracker' => 'App\\Domain\\Trackers\\IGLikesTracker',
                'platform' => 'Instagram',
                'name' => 'Лайки',
                'pipeline' => [
                    SetRegion::class,
                    CheckHasLinkAndCount::class,
                    SetImgFromLinkAsMediaUrl::class,
                    SetLoginFromLinkAsMediaUrl::class,
                    ExtractCodeFromLink::class,
                    SetRestylerIgScraper::class,
                    ScrapeIgMedia::class,
                    SetOneOrder::class,
                    SetDefaultPriceFromCount::class
                ],
                'labels' => ["TYPE_LIKES", "DISCOUNT_LIKES", "VISIBLE", "ENABLED", "CLIENT_LIGHT4"],
            ),
            array (
                'id' => 117,
                'title' => 'Подписчиков',
                'tag' => UserService::INSTAGRAM_SUBS_LIGHT4,
                'splitter' => 'App\\Domain\\Splitters\\DefaultSplitter',
                'config' => [
                    [
                        'name' => Slots::INSTAGRAM_SUBS_MAIN_EVERVE,
                        'service_class' => AEverve::class,
                        'order' => 1,
                        'min' => 50,
                        'max' => 2500,
                        'remote_params' => [
                            'category_id' => 19,
                            'order_price' => 0.002,
                        ],
                        'net_cost' => [
                            'amount' => 100,
                            'local' => 14.21,
                            'mode' => AbstractService::NET_COST_LOCAL,
                            'auto' => 0,
                            'auto_timestamp' => null,
                        ],
                        'isEnabled' => true,
                        'count_extra_percent' => 0,
                    ],
                    [
                        'name' => Slots::INSTAGRAM_SUBS_MAIN_VKSERFING,
                        'service_class' => AVkserfing::class,
                        'order' => 2,
                        'min' => 5,
                        'max' => 6000,
                        'remote_params' => [
                            'type' => 'instagram_follower'
                        ],
                        'net_cost' => [
                            'amount' => 100,
                            'local' => 25,
                            'mode' => AbstractService::NET_COST_LOCAL,
                            'auto' => 0,
                            'auto_timestamp' => null,
                        ],
                        'isEnabled' => false,
                        'count_extra_percent' => 0,
                    ],
                    [
                        'name' => Slots::INSTAGRAM_SUBS_MAIN_NAKRUTKA,
                        'service_class' => ANakrutka::class,
                        'order' => 3,
                        'min' => 100,
                        'max' => 10_000_000,
                        'remote_params' => [
                            'service' => 3,
                        ],
                        'net_cost' => [
                            'amount' => 100,
                            'local' => 20,
                            'mode' => AbstractService::NET_COST_LOCAL,
                            'auto' => 0,
                            'auto_timestamp' => null,
                        ],
                        'isEnabled' => true,
                        'count_extra_percent' => 20,
                    ],
                    [
                        'name' => Slots::INSTAGRAM_SUBS_MAIN_SOCGRESS,
                        'service_class' => ASocgress::class,
                        'order' => 4,
                        'min' => 20,
                        'max' => 500_000,
                        'remote_params' => [
                            'service_id' => 88,
                            'network' => 'instagram',
                            'speed' => 'slow',
                        ],
                        'net_cost' => [
                            'amount' => 100,
                            'local' => 0, // service not available
                            'mode' => AbstractService::NET_COST_LOCAL,
                            'auto' => 0,
                            'auto_timestamp' => null,
                        ],
                        'isEnabled' => false,
                        'count_extra_percent' => 0,
                    ],
                ],
                'img' => '/svg/media-player.svg',
                'created_at' => '2020-07-02 23:33:47+03',
                'updated_at' => '2021-07-11 09:08:26+03',
                'description' => '{"min":null,"max":null}',
                'card' => '["\\u043f\\u0440\\u0435\\u043c\\u0438\\u0443\\u043c \\u043a\\u0430\\u0447\\u0435\\u0441\\u0442\\u0432\\u043e","<strong>\\u0433\\u0430\\u0440\\u0430\\u043d\\u0442\\u0438\\u044f \\u043e\\u0442 \\u0441\\u043f\\u0438\\u0441\\u0430\\u043d\\u0438\\u0439<\\/strong>","100% \\u0441 \\u0444\\u043e\\u0442\\u043e \\u0438 \\u043f\\u043e\\u0441\\u0442\\u0430\\u043c\\u0438","\\u0431\\u044b\\u0441\\u0442\\u0440\\u044b\\u0439 \\u0437\\u0430\\u043f\\u0443\\u0441\\u043a","\\u043d\\u0435\\u0442 \\u0431\\u0430\\u043d\\u0430","\\u0434\\u043e 10 \\u0442\\u044b\\u0441. \\u0432 \\u0441\\u0443\\u0442\\u043a\\u0438"]',
                'local_validation' => NULL,
                'local_checker' => NULL,
                'tracker' => 'App\\Domain\\Trackers\\IGFollowTracker',
                'platform' => 'Instagram',
                'name' => 'Подписчики',
                'pipeline' => [
                    SetRegion::class,
                    CheckHasLinkAndCount::class,
                    SetLoginFromLink::class,
                    SetImgFromLogin::class,
                    SetLinkAsFullUrlWithLogin::class,
                    SetRestylerIgScraper::class,
                    ScrapeIgProfile::class,
                    SetOneOrder::class,
                    SetDefaultPriceFromCount::class
                ],
                'labels' => ["TYPE_SUBS", "DISCOUNT_SUBS", "VISIBLE", "ENABLED", "CLIENT_LIGHT4"],
            ),
            array(
                'id' => 118,
                'title' => 'Просмотров',
                'tag' => UserService::INSTAGRAM_VIEWS_VIDEO_LIGHT4,
                'splitter' => DefaultSplitter::class,
                'config' => [
                    [
                        'name' => Slots::INSTAGRAM_VIEWS_VIDEO_MAIN_NAKRUTKA,
                        'service_class' => ANakrutka::class,
                        'order' => 1,
                        'min' => 100,
                        'max' => 100_000,
                        'remote_params' => [
                            'service' => 7,
                        ],
                        'net_cost' => [
                            'amount' => 100,
                            'local' => 0.6,
                            'mode' => AbstractService::NET_COST_LOCAL,
                            'auto' => 0,
                            'auto_timestamp' => null,
                        ],
                        'isEnabled' => true,
                        'count_min' => 0,
                    ]
                ],
                'img' => '/svg/media-player.svg',
                'created_at' => '2020-07-02 23:33:47+03',
                'updated_at' => '2021-06-07 22:33:59+03',
                'description' => NULL,
                'card' => '["\\u0432\\u044b\\u0441\\u043e\\u043a\\u043e\\u0435 \\u043a\\u0430\\u0447\\u0435\\u0441\\u0442\\u0432\\u043e","\\u043c\\u0433\\u043d\\u043e\\u0432\\u0435\\u043d\\u043d\\u044b\\u0439 \\u0437\\u0430\\u043f\\u0443\\u0441\\u043a","\\u043d\\u0435\\u0442 \\u0431\\u0430\\u043d\\u0430","\\u0434\\u043e 300 \\u0442\\u044b\\u0441. \\u0432 \\u0441\\u0443\\u0442\\u043a\\u0438"]',
                'local_validation' => NULL,
                'local_checker' => NULL,
                'tracker' => 'App\\Domain\\Trackers\\IGViewsTracker',
                'platform' => 'Instagram',
                'name' => 'Просмотры видео',
                'pipeline' => [
                    SetRegion::class,
                    CheckHasLinkAndCount::class,
                    SetImgFromLinkAsMediaUrl::class,
                    SetLoginFromLinkAsMediaUrl::class,
                    ExtractCodeFromLink::class,
                    SetRestylerIgScraper::class,
                    ScrapeIgMedia::class,
                    SetOneOrder::class,
                    SetDefaultPriceFromCount::class,
                ],
                'labels' => ["TYPE_VIEWS", "DISCOUNT_VIEWS", "VISIBLE", "ENABLED", "CLIENT_LIGHT4"],
            ),
        );

        foreach($arrayOfServices as $service) {
            UserService::create($service);
        }
    }
}
