<?php

namespace Tests\Feature;

use App\Domain\Models\Slots;
use App\Domain\Services\AbstractService;
use App\Domain\Services\Everve\AEverve;
use App\Domain\Services\Nakrutka\ANakrutka;
use App\Domain\Services\Socgress\ASocgress;
use App\Domain\Services\Vkserfing\AVkserfing;
use App\UserService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\TF\TFHelpers;

class SlotsTest extends TestCase
{
    use DatabaseMigrations;

    protected $slotsArray = [
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
    ];

    public function setUp(): void
    {
        parent::setUp();
        TFHelpers::runTestSeeders();
    }

    private function applyNewConfig($tag)
    {
        UserService::where('tag', $tag)
            ->firstOrFail()
            ->update([ 'config' => $this->slotsArray ]);
    }

    public function testGetSlotFromArrayByName()
    {
        $slot1 = Slots::getSlotFromArray(
            Slots::INSTAGRAM_LIKES_LK_VKSERFING, $this->slotsArray);
        $this->assertEquals($this->slotsArray[0], $slot1);

        $slot2 = Slots::getSlotFromArray(
            Slots::INSTAGRAM_LIKES_LK_SOCGRESS, $this->slotsArray);
        $this->assertEquals($this->slotsArray[2], $slot2);
    }

    public function searchProvider()
    {
        return [
            [Slots::INSTAGRAM_LIKES_LK_VKSERFING, 0],
            [Slots::INSTAGRAM_LIKES_LK_EVERVE, 1],
            [Slots::INSTAGRAM_LIKES_LK_SOCGRESS, 2],
            [Slots::INSTAGRAM_LIKES_LK_NAKRUTKA, 3],
            [Str::random(8), -1],
        ];
    }

    /** @dataProvider searchProvider */
    public function testSearchForSlotInService($slotName, $index)
    {
        $this->applyNewConfig(UserService::INSTAGRAM_LIKES_LK);
        $us = UserService::where('tag', UserService::INSTAGRAM_LIKES_LK)
            ->firstOrFail();

        $i = Slots::searchForSlotInService($slotName, $us);
        $this->assertEquals($index, $i);
    }

    public function testMergeSlotConfigAppendValue()
    {
        $this->applyNewConfig(UserService::INSTAGRAM_LIKES_LK);
        Slots::mergeSlotConfig(Slots::INSTAGRAM_LIKES_LK_VKSERFING, [
            'key1' => 'value1',
        ]);
        $foundConfig = Slots::getConfig(Slots::INSTAGRAM_LIKES_LK_VKSERFING);
        $this->assertEquals([
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
            'key1' => 'value1',
        ], $foundConfig);
    }

    public function testMergeSlotConfigRewriteValues()
    {
        $this->applyNewConfig(UserService::INSTAGRAM_LIKES_LK);
        Slots::mergeSlotConfig(Slots::INSTAGRAM_LIKES_LK_VKSERFING, [
            'order' => 5,
            'max' => 100,
            'remote_params' => [
                'xxx' => 'yyy',
            ],
        ]);
        $foundConfig = Slots::getConfig(Slots::INSTAGRAM_LIKES_LK_VKSERFING);
        $this->assertEquals([
            'name' => Slots::INSTAGRAM_LIKES_LK_VKSERFING,
            'service_class' => AVkserfing::class,
            'order' => 5,
            'min' => 5,
            'max' => 100,
            'remote_params' => [
                'xxx' => 'yyy',
            ],
            'net_cost' => [
                'amount' => 100,
                'local' => 5,
                'mode' => AbstractService::NET_COST_LOCAL,
                'auto' => 123,
                'auto_timestamp' => null,
            ],
        ], $foundConfig);
    }

    public function testDeepMergeSlotConfig()
    {
        $this->applyNewConfig(UserService::INSTAGRAM_LIKES_LK);
        Slots::deepMergeSlotConfig(
            Slots::INSTAGRAM_LIKES_LK_VKSERFING,
            'remote_params',
            [ 'k1' => 'v1' ]
        );
        $foundConfig = Slots::getConfig(Slots::INSTAGRAM_LIKES_LK_VKSERFING);
        $this->assertEquals([
            'name' => Slots::INSTAGRAM_LIKES_LK_VKSERFING,
            'service_class' => AVkserfing::class,
            'order' => 1,
            'min' => 5,
            'max' => 5,
            'remote_params' => [
                'type' => 'instagram_like',
                'k1' => 'v1',
            ],
            'net_cost' => [
                'amount' => 100,
                'local' => 5,
                'mode' => AbstractService::NET_COST_LOCAL,
                'auto' => 123,
                'auto_timestamp' => null,
            ],
        ], $foundConfig);

    }

    public function testMergeSlotNetCost()
    {
        $this->applyNewConfig(UserService::INSTAGRAM_LIKES_LK);
        Slots::mergeSlotNetCost(Slots::INSTAGRAM_LIKES_LK_VKSERFING, [
            'k1' => 'v1',
            'k2' => 'v2',
            'auto' => 123456,
        ]);
        $foundConfig = Slots::getConfig(Slots::INSTAGRAM_LIKES_LK_VKSERFING);
        $this->assertEquals([
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
                'auto' => 123456,
                'auto_timestamp' => null,
                'k1' => 'v1',
                'k2' => 'v2',
            ],
        ], $foundConfig);
    }
}
