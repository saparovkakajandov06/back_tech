<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Domain\Models\Labels;
use App\Domain\Services\Nakrutka\ANakrutka;
use App\Domain\Splitters\DefaultSplitter;
use App\Domain\Transformers\Instagram\SetImgFromLinkAsMediaUrl;
use App\Domain\Transformers\Instagram\SetLoginFromLinkAsMediaUrl;
use App\Domain\Transformers\SetDefaultPriceFromCount;
use App\Domain\Transformers\SetOneOrder;
use App\Domain\Validators\CheckHasLinkAndCount;
use App\Domain\Validators\CheckLinkAsMediaUrl;
use App\Domain\Validators\CheckUserHasEnoughFunds;
use App\UserService;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(UserService::class, function (Faker $faker) {
    return [
        'title' => 'Тестовый сервис',
        'tag' => 'TEST_SVC_' . Str::random(6),
        'labels' => [
            Labels::TYPE_LIKES,
            Labels::DISCOUNT_LIKES,
            Labels::VISIBLE,
            Labels::ENABLED,
            Labels::CLIENT_LK,
        ],
        'pipeline' => [
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
            ANakrutka::class => [
                'order' => 1,
                'min' => 1,
                'max' => 100,
                'service_id' => 88,
            ],
        ],
    ];
});
