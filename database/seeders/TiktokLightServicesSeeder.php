<?php

namespace Database\Seeders;

use App\Domain\Models\Labels;
use App\Domain\Models\Slots;
use App\Domain\Services\AbstractService;
use App\Domain\Services\Everve\AEverve;
use App\Domain\Services\Nakrutka\ANakrutka;
use App\Domain\Services\Socgress\ASocgress;
use App\Domain\Services\Vkserfing\AVkserfing;
use App\Domain\Services\Vtope\AVtope;
use App\Domain\Transformers\General\SetRegion;
use App\Domain\Transformers\Parsers\ParseTiktokLink;
use App\Domain\Transformers\Parsers\ParseTiktokLogin;
use App\Domain\Transformers\SaveImg;
use App\Domain\Transformers\Scrapers\ScrapeTiktokProfile;
use App\Domain\Transformers\Scrapers\ScrapeTiktokVideo;
use App\Domain\Transformers\Scrapers\SetBestExperienceTiktokScraper;
use App\Domain\Transformers\Scrapers\SetTiktokScraper;
use App\Domain\Transformers\SetDefaultPriceFromCount;
use App\Domain\Transformers\SetOneOrder;
use App\Domain\Validators\CheckHasLinkAndCount;
use App\Domain\Validators\CheckHasLoginAndCount;
use App\UserService;
use Illuminate\Database\Seeder;


class TiktokLightServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $arrayOfServices = array(
            /* |=========================|
                 * | Tiktok                  |
                 * |=========================|
                 * */

            array(
                'title' => 'Лайки',
                'tag' => UserService::TIKTOK_LIKES_LIGHT4,
                'splitter' => 'App\\Domain\\Splitters\\DefaultSplitter',
                'config' => [
                    [
                        'name' => Slots::TIKTOK_LIKES_MAIN_VTOPE,
                        'service_class' => AVtope::class,
                        'order' => 1,
                        'min' => 5,
                        'max' => 5000,
                        'remote_params' => [
                            'method' => 'add',
                            'service' => 'k',
                            'type' => 'like',
                        ],
                        'net_cost' => [
                            'amount' => 100,
                            'local' => 0, // ???
                            'mode' => AbstractService::NET_COST_LOCAL,
                            'auto' => 0,
                            'auto_timestamp' => null,
                        ],
                        'isEnabled' => false,
                    ],
                    [
                        'name' => Slots::TIKTOK_LIKES_MAIN_NAKRUTKA,
                        'service_class' => ANakrutka::class,
                        'order' => 2,
                        'min' => 100,
                        'max' => 5000,
                        'remote_params' => [
                            'service' => 96,
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
                        'name' => Slots::TIKTOK_LIKES_MAIN_SOCGRESS,
                        'service_class' => ASocgress::class,
                        'order' => 3,
                        'min' => 5,
                        'max' => 1000,
                        'remote_params' => [
                            'service_id' => 162,
                            'network' => 'tiktok', // ??? 'instagram',
                            'speed' => 'slow',
                        ],
                        'net_cost' => [
                            'amount' => 100,
                            'local' => 7,
                            'mode' => AbstractService::NET_COST_LOCAL,
                            'auto' => 0,
                            'auto_timestamp' => null,
                        ],
                        'count_extra_percent' => 0,
                        'isEnabled' => false,
                    ],
                    [
                        'name' => Slots::TIKTOK_LIKES_MAIN_VKSERFING,
                        'service_class' => AVkserfing::class,
                        'order' => 4,
                        'min' => 5,
                        'max' => 1_000_000,
                        'remote_params' => [
                            'type' => 'tiktok_like',
                        ],
                        'net_cost' => [
                            'amount' => 100,
                            'local' => 0, // ???
                            'mode' => AbstractService::NET_COST_LOCAL,
                            'auto' => 123,
                            'auto_timestamp' => null,
                        ],
                        'isEnabled' => true,
                        'count_extra_percent' => 0,
                    ],
                    [
                        'name' => Slots::TIKTOK_LIKES_MAIN_EVERVE,
                        'service_class' => AEverve::class,
                        'order' => 5,
                        'min' => 100,
                        'max' => 1000,
                        'remote_params' => [
                            'category_id' => 48,
                            'order_price' => 0.001,
                        ],
                        'net_cost' => [
                            'amount' => 100,
                            'local' => 7.1,
                            'mode' => AbstractService::NET_COST_LOCAL,
                            'auto' => 123,
                            'auto_timestamp' => null,
                        ],
                        'isEnabled' => false,
                        'count_extra_percent' => 10,
                    ]
                ],
                'img' => '/svg/media-player.svg',
                'created_at' => NULL,
                'updated_at' => '2021-07-13 16:50:35+03',
                'description' => '{"min":null,"max":null}',
                'card' => '["\\u0432\\u044b\\u0441\\u043e\\u043a\\u043e\\u0435 \\u043a\\u0430\\u0447\\u0435\\u0441\\u0442\\u0432\\u043e","\\u043c\\u0433\\u043d\\u043e\\u0432\\u0435\\u043d\\u043d\\u044b\\u0439 \\u0437\\u0430\\u043f\\u0443\\u0441\\u043a","\\u0441 \\u0430\\u0432\\u0430\\u0442\\u0430\\u0440\\u043a\\u0430\\u043c\\u0438","\\u043d\\u0435\\u0442 \\u0431\\u0430\\u043d\\u0430","\\u0434\\u043e 10 \\u0442\\u044b\\u0441. \\u0432 \\u0441\\u0443\\u0442\\u043a\\u0438"]',
                'local_validation' => NULL,
                'local_checker' => NULL,
                'tracker' => 'App\\Domain\\Trackers\\TKTLikeTracker',
                'platform' => 'Tiktok',
                'name' => 'Лайки',
                'pipeline' => [
                    SetRegion::class,
                    CheckHasLinkAndCount::class,
                    ParseTiktokLink::class,
                    SetTiktokScraper::class,
                    ScrapeTiktokVideo::class,
                    SetOneOrder::class,
                    SetDefaultPriceFromCount::class,
                    SaveImg::class,
                ],
                'labels' => ["TYPE_LIKES", "DISCOUNT_LIKES", "VISIBLE", "ENABLED", "CLIENT_LIGHT4"],
            ),

            array (
                'title' => 'Подписчики',
                'tag' => UserService::TIKTOK_SUBS_LIGHT4,
                'splitter' => 'App\\Domain\\Splitters\\DefaultSplitter',
                'config' => [
                    [
                        'name' => Slots::TIKTOK_SUBS_MAIN_VTOPE,
                        'service_class' => AVtope::class,
                        'order' => 1,
                        'min' => 5,
                        'max' => 100,
                        'remote_params' => [
                            'method' => 'add',
                            'service' => 'k',
                            'type' => 'follower',
                        ],
                        'net_cost' => [
                            'amount' => 100,
                            'local' => 0, // ???
                            'mode' => AbstractService::NET_COST_LOCAL,
                            'auto' => 0,
                            'auto_timestamp' => null,
                        ],
                        'isEnabled' => false,
                        'count_extra_percent' => 0,
                    ],
                    [
                        'name' => Slots::TIKTOK_SUBS_MAIN_SOCGRESS,
                        'service_class' => ASocgress::class,
                        'order' => 2,
                        'min' => 100,
                        'max' => 500,
                        'remote_params' => [
                            'service_id' => 148,
                            'network' => 'tiktok',
                            'speed' => 'slow',
                        ],
                        'net_cost' => [
                            'amount' => 100,
                            'local' => 7,
                            'mode' => AbstractService::NET_COST_LOCAL,
                            'auto' => 0,
                            'auto_timestamp' => null,
                        ],
                        'count_extra_percent' => 0,
                        'isEnabled' => false,
                    ],
                    [
                        'name' => Slots::TIKTOK_SUBS_MAIN_VKSERFING,
                        'service_class' => AVkserfing::class,
                        'order' => 3,
                        'min' => 5,
                        'max' => 1_000_000,
                        'remote_params' => [
                            'type' => 'tiktok_follower',
                        ],
                        'net_cost' => [
                            'amount' => 100,
                            'local' => 0, // ???
                            'mode' => AbstractService::NET_COST_LOCAL,
                            'auto' => 123,
                            'auto_timestamp' => null,
                        ],
                        'isEnabled' => true,
                    ],
                    [
                        'name' => Slots::INSTAGRAM_SUBS_MAIN_NAKRUTKA,
                        'service_class' => ANakrutka::class,
                        'order' => 4,
                        'min' => 50,
                        'max' => 1_000_000,
                        'remote_params' => [
                            'service' => 97,
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
                    ]
                ],
                'img' => '/svg/media-player.svg',
                'created_at' => NULL,
                'updated_at' => '2021-07-09 10:08:27+03',
                'description' => '{"min":null,"max":null}',
                'card' => '["\\u0432\\u044b\\u0441\\u043e\\u043a\\u043e\\u0435 \\u043a\\u0430\\u0447\\u0435\\u0441\\u0442\\u0432\\u043e","\\u043c\\u0433\\u043d\\u043e\\u0432\\u0435\\u043d\\u043d\\u044b\\u0439 \\u0437\\u0430\\u043f\\u0443\\u0441\\u043a","\\u0441 \\u0430\\u0432\\u0430\\u0442\\u0430\\u0440\\u043a\\u0430\\u043c\\u0438","\\u043d\\u0435\\u0442 \\u0431\\u0430\\u043d\\u0430","\\u0434\\u043e 10 \\u0442\\u044b\\u0441. \\u0432 \\u0441\\u0443\\u0442\\u043a\\u0438"]',
                'local_validation' => NULL,
                'local_checker' => NULL,
                'tracker' => 'App\\Domain\\Trackers\\TKTFollowTracker',
                'platform' => 'Tiktok',
                'name' => 'Подписчики',
                'pipeline' => [
                    SetRegion::class,
                    ParseTiktokLogin::class,
                    CheckHasLoginAndCount::class,
                    SetTiktokScraper::class,
                    ScrapeTiktokProfile::class,
                    SetOneOrder::class,
                    SetDefaultPriceFromCount::class,
                    SaveImg::class,
                ],
                'labels' => ["TYPE_SUBS", "DISCOUNT_SUBS", "VISIBLE", "ENABLED", "CLIENT_LIGHT4"],
            ),

            array (
                'title' => 'Просмотры',
                'tag' => UserService::TIKTOK_VIEWS_LIGHT4,
                'splitter' => 'App\\Domain\\Splitters\\DefaultSplitter',
                'config' => [
                    [
                        'name' => Slots::TIKTOK_VIEWS_MAIN_NAKRUTKA,
                        'service_class' => ANakrutka::class,
                        'order' => 1,
                        'min' => 100,
                        'max' => 100_000,
                        'remote_params' => [
                            'service' => 95,
                        ],
                        'net_cost' => [
                            'amount' => 100,
                            'local' => 1.69,
                            'mode' => AbstractService::NET_COST_LOCAL,
                            'auto' => 0,
                            'auto_timestamp' => null,
                        ],
                        'isEnabled' => true,
                    ],
                    [
                        'name' => Slots::TIKTOK_VIEWS_MAIN_NAKRUTKA_2,
                        'service_class' => ANakrutka::class,
                        'order' => 2,
                        'min' => 100,
                        'max' => 100_000,
                        'remote_params' => [
                            'service' => 98,
                        ],
                        'net_cost' => [
                            'amount' => 100,
                            'local' => 4.187,
                            'mode' => AbstractService::NET_COST_LOCAL,
                            'auto' => 0,
                            'auto_timestamp' => null,
                        ],
                        'isEnabled' => false,
                    ],
                    [
                        'name' => Slots::TIKTOK_VIEWS_MAIN_SOCGRESS,
                        'service_class' => ASocgress::class,
                        'order' => 3,
                        'min' => 100,
                        'max' => 100_000,
                        'remote_params' => [
                            'service_id' => 68,
                            'network' => 'tiktok',
                            'speed' => 'slow',
                        ],
                        'net_cost' => [
                            'amount' => 100,
                            'local' => 0, // ???
                            'mode' => AbstractService::NET_COST_LOCAL,
                            'auto' => 0,
                            'auto_timestamp' => null,
                        ],
                        'isEnabled' => true,
                        'count_extra_percent' => 0,
                    ]
                ],
                'img' => '/svg/media-player.svg',
                'created_at' => NULL,
                'updated_at' => '2021-06-19 11:04:57+03',
                'description' => NULL,
                'card' => '["\\u0432\\u044b\\u0441\\u043e\\u043a\\u043e\\u0435 \\u043a\\u0430\\u0447\\u0435\\u0441\\u0442\\u0432\\u043e","\\u043c\\u0433\\u043d\\u043e\\u0432\\u0435\\u043d\\u043d\\u044b\\u0439 \\u0437\\u0430\\u043f\\u0443\\u0441\\u043a","\\u0441 \\u0430\\u0432\\u0430\\u0442\\u0430\\u0440\\u043a\\u0430\\u043c\\u0438","\\u043d\\u0435\\u0442 \\u0431\\u0430\\u043d\\u0430","\\u0434\\u043e 10 \\u0442\\u044b\\u0441. \\u0432 \\u0441\\u0443\\u0442\\u043a\\u0438"]',
                'local_validation' => NULL,
                'local_checker' => NULL,
                'tracker' => 'App\\Domain\\Trackers\\TKTViewsTracker',
                'platform' => 'Tiktok',
                'name' => 'Просмотры видео',
                'pipeline' => [
                    SetRegion::class,
                    CheckHasLinkAndCount::class,
                    ParseTiktokLink::class,
                    SetTiktokScraper::class,
                    ScrapeTiktokVideo::class,
                    SetOneOrder::class,
                    SetDefaultPriceFromCount::class,
                    SaveImg::class,
                ],
                'labels' => ["TYPE_VIEWS", "DISCOUNT_VIEWS", "VISIBLE", "ENABLED", "CLIENT_LIGHT4"],
            ),
        );

        foreach($arrayOfServices as $service) {
            UserService::where('tag', $service['tag'])->delete();
            UserService::create($service);
        }
    }
}
