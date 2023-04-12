<?php

namespace Database\Seeders;

use App\Domain\Models\Labels;
use App\Domain\Models\Slots;
use App\Domain\Services\AbstractService;
use App\Domain\Services\Everve\AEverve;
use App\Domain\Services\Fake\AFake;
use App\Domain\Services\Fake\AFake2;
use App\Domain\Services\Nakrutka\ANakrutka;
use App\Domain\Services\NakrutkaAuto\ANakrutkaAuto;
use App\Domain\Services\Socgress\ASocgress;
use App\Domain\Services\Vkserfing\AVkserfing;
use App\Domain\Services\VkserfingAuto\AVkserfingAuto;
use App\Domain\Splitters\DefaultSplitter;
use App\Domain\Splitters\NakrutkaOneAutoChunk;
use App\Domain\Splitters\VkSerfSplitter;
use App\Domain\Transformers\General\SetRegion;
use App\Domain\Transformers\Instagram\SetImgFromLinkAsMediaUrl;
use App\Domain\Transformers\Instagram\SetImgFromLogin;
use App\Domain\Transformers\Instagram\SetLinkAsFullUrlWithLogin;
use App\Domain\Transformers\Instagram\SetLoginFromLink;
use App\Domain\Transformers\Instagram\SetLoginFromLinkAsMediaUrl;
use App\Domain\Transformers\Parsers\ParseTiktokLogin;
use App\Domain\Transformers\SaveImg;
use App\Domain\Transformers\Scrapers\ScrapeTiktokProfile;
use App\Domain\Transformers\Scrapers\SetKirtanTiktokScraper;
use App\Domain\Transformers\SetAutoPrice;
use App\Domain\Transformers\SetDefaultPriceFromCount;
use App\Domain\Transformers\SetNormalAutoPrice;
use App\Domain\Transformers\SetOneOrder;
use App\Domain\Validators\CheckHasLinkAndCount;
use App\Domain\Validators\CheckHasLoginCountPosts;
use App\Domain\Validators\CheckHasLoginMinMaxPosts;
use App\Domain\Validators\CheckHasTargets;
use App\Domain\Validators\CheckLinkAsMediaUrl;
use App\Domain\Validators\CheckLoginIsNotPrivate;
use App\Domain\Validators\CheckUserHasEnoughFunds;
use App\Transaction;
use App\UserService;
use App\USPrice;
use Illuminate\Database\Seeder;

// used class

class TestUserServicesTableSeeder extends Seeder
{
    public function run(): void
    {

// 1. cut pipeline - add scraper model
// 2. send fields to UI - fields array
// 3. conversion goes to backend service - api/convert/type

//      login => scrapedModel
//      $params['meta'] = IgUser::fromLogin($login);
//      ScrapeInstagramUser::class;
//
//      /api/convert/instagram_login => error | login
//      convert('buzova', 'instagram_login') == 'buzova';


        /****************************************************
         * Лайки
         *****************************************************/

        //на один пост
        UserService::create([
            'title' => 'Быстрые лайки - 19 руб',
            'tag' => UserService::INSTAGRAM_LIKES_LK,
            'labels' => [
                Labels::TYPE_LIKES,
                Labels::DISCOUNT_LIKES,
                Labels::VISIBLE,
                Labels::ENABLED,
                Labels::CLIENT_LK,
            ],
            'pipeline' => [
                SetRegion::class,
                CheckHasLinkAndCount::class,
                CheckLinkAsMediaUrl::class,
                SetImgFromLinkAsMediaUrl::class,
                SetLoginFromLinkAsMediaUrl::class,

                SetOneOrder::class,
                SetDefaultPriceFromCount::class,
                CheckUserHasEnoughFunds::class,
            ],
            'splitter' => DefaultSplitter::class,
            'config' => [
                [
                    'name' => Slots::INSTAGRAM_LIKES_LK_VKSERFING,
                    'service_class' => AVkserfing::class,
                    'order' => 1,
                    'min' => 5,
                    'max' => 5,
                    'remote_params' => [
                        'type' => 'instagram_like',
                    ],
                    'net_cost' => [
                        'amount' => 100,
                        'local' => 5,
                        'mode' => AbstractService::NET_COST_LOCAL,
                        'auto' => 123,
                        'auto_timestamp' => null,
                    ],
                ],
                [
                    'name' => Slots::INSTAGRAM_LIKES_LK_EVERVE,
                    'service_class' => AEverve::class,
                    'order' => 2,
                    'min' => 5,
                    'max' => 5,
                    'remote_params' => [
                        'category_id' => 18,
                        'order_price' => 0.001,
                    ],
                    'net_cost' => [
                        'amount' => 100,
                        'local' => 7.1,
                        'mode' => AbstractService::NET_COST_LOCAL,
                        'auto' => 123,
                        'auto_timestamp' => null,
                    ],
                ],
                [
                    'name' => Slots::INSTAGRAM_LIKES_LK_SOCGRESS,
                    'service_class' => ASocgress::class,
                    'order' => 3,
                    'min' => 5,
                    'max' => 5,
                    'remote_params' => [
                        'service_id' => 33,
                        'network' => 'instagram',
                        'speed' => 'slow',
                    ],
                    'net_cost' => [
                        'amount' => 100,
                        'local' => 7,
                        'mode' => AbstractService::NET_COST_DISABLED,
                        'auto' => 123,
                        'auto_timestamp' => null,
                    ],
                ],
                [
                    'name' => Slots::INSTAGRAM_LIKES_LK_NAKRUTKA,
                    'service_class' => ANakrutka::class,
                    'order' => 4,
                    'min' => 5,
                    'max' => 5,
                    'remote_params' => [
                        'service' => 81,
                    ],
                    'net_cost' => [
                        'amount' => 100,
                        'local' => 4.187,
                        'mode' => AbstractService::NET_COST_DISABLED,
                        'auto' => 123,
                        'auto_timestamp' => null,
                    ],
                ]
            ],
            'img' => '/svg/like.svg',
            'description' => [
                'startup' => 'В течение 1-5 минут',
                'speed' => '300 - 500 в минуту',
                'min' => '100',
                'max' => '10000',
                'requirements' => 'Должен быть открытым и иметь аватарку',
                'details' => 'Лайкают качественные офферы. ' .
                    'Охват повышает статистику страницы.' .
                    'Идеально подходят для вывода в топ.' .
                    'Моментальный запуск.',
            ],
        ]);

        USPrice::create([
            'tag' => UserService::INSTAGRAM_LIKES_LK,
            Transaction::CUR_RUB => [
                1 => 0.19,
                1000 => 0.185,
                5000 => 0.182,
                10000 => 0.179,
                25000 => 0.178,
                50000 => 0.175,
                100000 => 0.175,
            ],
            Transaction::CUR_USD => [
                1 => 0.010,
                1000 => 0.009,
                5000 => 0.008,
                10000 => 0.007,
                25000 => 0.006,
                50000 => 0.005,
                100000 => 0.004,
            ],
            Transaction::CUR_EUR => [
                1 => 0.010,
                1000 => 0.009,
                5000 => 0.008,
                10000 => 0.007,
                25000 => 0.006,
                50000 => 0.005,
                100000 => 0.004,
            ],
            Transaction::CUR_TRY => null,
            Transaction::CUR_UAH => null,
        ]);
        // ----------------------------------------

        UserService::create([
            'title' => 'Лайков',
            'tag' => UserService::INSTAGRAM_LIKES_MAIN,
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
                SetOneOrder::class,
                SetDefaultPriceFromCount::class
            ],
            'labels' => ["TYPE_LIKES", "DISCOUNT_VIEWS", "VISIBLE", "ENABLED", "CLIENT_MAIN"],
        ]);

        USPrice::create([
            'tag' => UserService::INSTAGRAM_LIKES_MAIN,
            'EUR' => [
                1 => 0.0099,
                1000 => 0.00699,
                5000 => 0.005998,
                10000 => 0.004999,
                25000 => 0.0047996,
                50000 => 0.0039998,
                100000 => 0.0037999,
            ],
            'USD' => [
                1 => 0.0099,
                1000 => 0.00699,
                5000 => 0.005998,
                10000 => 0.004999,
                25000 => 0.0047996,
                50000 => 0.0039998,
                100000 => 0.0037999,
            ],
            'RUB' => [
                1 => 0.19,
                1000 => 0.185,
                5000 => 0.182,
                10000 => 0.179,
                25000 => 0.178,
                50000 => 0.175,
                100000 => 0.175,
            ],
            'TRY' => null,
            'UAH' => null,
        ]);

        // ----------------------------------------

        UserService::create([
            'title' => 'Fake Service Title',
            'tag' => UserService::FAKE_SERVICE_LK,
            'labels' => [
                Labels::TYPE_TEST,
                Labels::DISCOUNT_TEST,
                Labels::VISIBLE,
                Labels::ENABLED,
                Labels::CLIENT_LK,
            ],
            'pipeline' => [
                SetRegion::class,
                CheckHasLinkAndCount::class,
                CheckHasTargets::class,

                SetOneOrder::class,
                SetDefaultPriceFromCount::class,
                CheckUserHasEnoughFunds::class,
            ],
            'splitter' => DefaultSplitter::class,
            'config' => [
                [
                    'name' => Slots::FAKE_SERVICE_LK_FAKE_1,
                    'service_class' => AFake::class,
                    'order' => 1,
                    'min' => 1,
                    'max' => 1000,
                    'target' => 'target1',
                ],
                [
                    'name' => Slots::FAKE_SERVICE_LK_FAKE_2,
//                    'service_class' => AFake2::class,
                    'service_class' => AFake::class,
                    'order' => 2,
                    'min' => 1,
                    'max' => 1000,
                    'target' => 'target2',
                ],
            ],
            'img' => null,
            'description' => null,
            'tracker' => null,
        ]);

        USPrice::create([
            'tag' => UserService::FAKE_SERVICE_LK,
            Transaction::CUR_RUB => [
                1 => 1,
                1000 => 0.9,
                5000 => 0.8,
                10000 => 0.7,
                25000 => 0.6,
                50000 => 0.5,
                100000 => 0.4,
            ],
            Transaction::CUR_USD => [
                1 => 0.010,
                1000 => 0.009,
                5000 => 0.008,
                10000 => 0.007,
                25000 => 0.006,
                50000 => 0.005,
                100000 => 0.004,
            ],
            Transaction::CUR_EUR => [
                1 => 0.010,
                1000 => 0.009,
                5000 => 0.008,
                10000 => 0.007,
                25000 => 0.006,
                50000 => 0.005,
                100000 => 0.004,
            ],
            Transaction::CUR_TRY => null,
            Transaction::CUR_UAH => null,
        ]);
        // ---------------------

        UserService::create([
            'title' => 'С гарантией - 45 руб',
            'tag' => UserService::INSTAGRAM_SUBS_LK,
            'labels' => [
                Labels::TYPE_SUBS,
                Labels::DISCOUNT_SUBS,
                Labels::VISIBLE,
                Labels::ENABLED,
                Labels::CLIENT_LK,
            ],
            'pipeline' => [
                SetRegion::class,
                CheckHasLinkAndCount::class,
                SetLoginFromLink::class,
                CheckLoginIsNotPrivate::class,
                SetImgFromLogin::class,
                SetLinkAsFullUrlWithLogin::class,

                SetOneOrder::class,
                SetDefaultPriceFromCount::class,
                CheckUserHasEnoughFunds::class,
            ],
            'splitter' => DefaultSplitter::class,
            'config' => [
                [
                    'name' => Slots::INSTAGRAM_SUBS_LK_EVERVE,
                    'service_class' => AEverve::class,
                    'order' => 1,
                    'min' => 50,
                    'max' => 5000,
                    'remote_params' => [
                        'category_id' => 19,
                        'order_price' => 0.002001618123,
                    ],
                    'count_extra_percent' => 20,
                    'isEnabled' => true,
                    'net_cost' => [
                        'amount' => 100,
                        'local' => 14.21,
                        'mode' => AbstractService::NET_COST_LOCAL,
                    ],
                ],
                [
                    'name' => Slots::INSTAGRAM_SUBS_LK_VKSERFING,
                    'service_class' => AVkserfing::class,
                    'order' => 2,
                    'min' => 5,
                    'max' => 90,
                    'remote_params' => [
                        'type' => 'instagram_follower',
                    ],
                    'net_cost' => [
                        'amount' => 100,
                        'local' => 25,
                        'mode' => AbstractService::NET_COST_LOCAL,
                    ],
                ],
                [
                    'name' => Slots::INSTAGRAM_SUBS_LK_NAKRUTKA,
                    'service_class' => ANakrutka::class,
                    'order' => 3,
                    'min' => 100,
                    'max' => 10000,
                    'remote_params' => [
                        'service' => 3,
                    ],
                    'count_extra_percent' => 10,
                    'net_cost' => [
                        'amount' => 100,
                        'local' => 24.024,
                        'mode' => AbstractService::NET_COST_LOCAL,
                    ],
                    'isEnabled' => true
                ]
            ],
            'img' => '/svg/subs.svg',
            'description' => [],
        ]);

        USPrice::create([
            'tag' => UserService::INSTAGRAM_SUBS_LK,
            Transaction::CUR_RUB => [
                1 => 0.45,
                1000 => 0.44,
                5000 => 0.43,
                10000 => 0.41,
                25000 => 0.42,
                50000 => 0.394,
                100000 => 0.39,
            ],
            Transaction::CUR_USD => [
                1 => 0.010,
                1000 => 0.009,
                5000 => 0.008,
                10000 => 0.007,
                25000 => 0.006,
                50000 => 0.005,
                100000 => 0.004,
            ],
            Transaction::CUR_EUR => [
                1 => 0.010,
                1000 => 0.009,
                5000 => 0.008,
                10000 => 0.007,
                25000 => 0.006,
                50000 => 0.005,
                100000 => 0.004,
            ],
            Transaction::CUR_TRY => null,
            Transaction::CUR_UAH => null,
        ]);

        // ----------------------------------

        UserService::create([
            'title' => 'Автопросмотры - 5 руб',
            'tag' => UserService::INSTAGRAM_AUTO_VIEWS_LK,
            'splitter' => NakrutkaOneAutoChunk::class,
            'config' => [
                [
                    'name' => Slots::INSTAGRAM_AUTO_VIEWS_LK_NAKRUTKA_AUTO,
                    'service_class' => ANakrutkaAuto::class,
                    'order' => 1,
                    'min' => 100,
                    'max' => 50000,
                    'remote_params' => [
                        'service' => 15,
                        'delay' => 0,
                    ],
                    'net_cost' => [
                        'amount' => 100,
                        'local' => 0.6,
                        'mode' => AbstractService::NET_COST_LOCAL,
                        'auto' => 0,
                        'auto_timestamp' => null,
                    ],
                ],
            ],
            'img' => '/svg/autowatch.svg',
            'description' => null,
            'card' => [],
            'platform' => 'Instagram',
            'name' => 'Автопросмотры',
            'pipeline' => [
                SetRegion::class,
                CheckHasLoginMinMaxPosts::class,
                SetImgFromLogin::class,
                SetOneOrder::class,
                SetAutoPrice::class,
                SaveImg::class,
                CheckUserHasEnoughFunds::class,
            ],
            'labels' => [
                Labels::TYPE_AUTO,
                Labels::DISCOUNT_VIEWS,
                Labels::VISIBLE,
                Labels::ENABLED,
                Labels::CLIENT_LK,
            ],
        ]);

        USPrice::create([
            'tag' => UserService::INSTAGRAM_AUTO_VIEWS_LK,
            Transaction::CUR_RUB => [
                1 => 0.05,
                1000 => 0.045,
                5000 => 0.043,
                10000 => 0.04,
                25000 => 0.04,
                50000 => 0.037,
                100000 => 0.035,
            ],
            Transaction::CUR_USD => [
                1 => 0.0065,
                1000 => 0.00449,
                5000 => 0.002998,
                10000 => 0.002999,
                25000 => 0.0027996,
                50000 => 0.0023998,
                100000 => 0.0019999,
            ],
            Transaction::CUR_EUR => [
                1 => 0.0065,
                1000 => 0.00449,
                5000 => 0.002998,
                10000 => 0.002999,
                25000 => 0.0027996,
                50000 => 0.0023998,
                100000 => 0.0019999,
            ],
            Transaction::CUR_TRY => null,
            Transaction::CUR_UAH => null,
        ]);

        // -----------------
        UserService::create([
            'title' => 'Автолайки - 19 руб',
            'tag' => UserService::TIKTOK_AUTO_LIKES_LK,
            'splitter' => VkSerfSplitter::class,
            'config' => [
                [
                    'name' => Slots::TIKTOK_AUTO_LIKES_LK_VKSERFING_AUTO,
                    'service_class' => AVkserfingAuto::class,
                    'order' => 1,
                    'min' => 5,
                    'max' => 100000,
                    'remote_params' => [
                        'type' => 'tiktok_automatic_like',
                        'status' => 'on',
                    ],
                    'net_cost' => [
                        'amount' => 100,
                        'local' => 8,
                        'mode' => AbstractService::NET_COST_LOCAL,
                        'auto' => 0,
                        'auto_timestamp' => null,
                    ],
                ]
            ],
            'img' => '/svg/subs.svg',
            'description' => null,
            'card' => null,
            'tracker' => null,
            'platform' => 'Tiktok',
            'name' => 'Автолайки',
            'pipeline' => [
                SetRegion::class,
                CheckHasLoginCountPosts::class,
                ParseTiktokLogin::class,
                SetKirtanTiktokScraper::class,
                ScrapeTiktokProfile::class,
                SetOneOrder::class,
                SetNormalAutoPrice::class,
                CheckUserHasEnoughFunds::class
            ],
            'labels' => [
                Labels::TYPE_AUTO,
                Labels::DISCOUNT_BASIC,
                Labels::VISIBLE,
                Labels::ENABLED,
                Labels::CLIENT_LK,
            ]
        ]);

        USPrice::create([
            'tag' => UserService::TIKTOK_AUTO_LIKES_LK,
            Transaction::CUR_RUB => [
                1 => 0.19,
                1000 => 0.185,
                5000 => 0.182,
                10000 => 0.179,
                25000 => 0.178,
                50000 => 0.175,
                100000 => 0.175,
            ],
            Transaction::CUR_USD => [
                1 => 0.0099,
                1000 => 0.00699,
                5000 => 0.005998,
                10000 => 0.004999,
                25000 => 0.0047996,
                50000 => 0.0039998,
                100000 => 0.0037999,
            ],
            Transaction::CUR_EUR => [
                1 => 0.0099,
                1000 => 0.00699,
                5000 => 0.005998,
                10000 => 0.004999,
                25000 => 0.0047996,
                50000 => 0.0039998,
                100000 => 0.0037999,
            ],
            Transaction::CUR_TRY => null,
            Transaction::CUR_UAH => null,
        ]);
    }
}
