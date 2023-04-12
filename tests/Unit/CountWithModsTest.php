<?php

namespace Tests\Unit;

use App\Domain\Services\AbstractService;
use App\Domain\Services\Everve\AEverve;
use App\Domain\Services\Nakrutka\ANakrutka;
use App\Domain\Services\Socgress\ASocgress;
use App\Domain\Services\Vkserfing\AVkserfing;
use PHPUnit\Framework\TestCase;

class CountWithModsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testGetLocalCountWithModsExtra()
    {
        $config1 = [
            'service_class' => AVkserfing::class,
            'order' => 1,
            'min' => 10,
            'max' => 100,
            'count_extra_percent' => 100,
            'remote_params' => [
                'type' => 'instagram_like',
            ],
            "net_cost" => [
                "amount" => 100,
                "local" => 5,
                'mode' => 'local',
            ],
        ];

        $local = AbstractService::getLocalCountWithMods(10, $config1);
        $this->assertEquals(20, $local);

        $remote = AbstractService::getRemoteCountWithMods(10, $config1);
        $this->assertEquals(20, $remote);
    }

    public function testGetLocalCountWithModsMinYes()
    {
        $config2 = [
            'service_class' => AEverve::class,
            'order' => 2,
            'min' => 10,
            'max' => 100,
            'count_extra_percent' => 100,
            'count_min' => 25,
            'remote_params' => [
                'category_id' => 18,
                'order_price' => 0.001,
            ],
            'net_cost' => [
                'amount' => 100,
                'local' => 7.1,
                'mode' => 'local',
            ],
        ];

        $local = AbstractService::getLocalCountWithMods(10, $config2);
        $this->assertEquals(25, $local);

        $remote = AbstractService::getRemoteCountWithMods(10, $config2);
        $this->assertEquals(25, $remote);
    }

    public function testGetLocalCountWithModsMinNo()
    {
        $config3 = [
            'service_class' => ASocgress::class,
            'order' => 3,
            'min' => 10,
            'max' => 100,
            'count_extra_percent' => 10,
            'count_min' => 15,
            'remote_params' => [
                'service_id' => 33,
                'network' => 'instagram',
                'speed' => 'slow',
            ],
            'net_cost' => [
                'amount' => 100,
                'local' => 7,
                'mode' => 'local',
            ],
        ];

        $local = AbstractService::getLocalCountWithMods(20, $config3);
        $this->assertEquals(22, $local);

        $remote = AbstractService::getRemoteCountWithMods(20, $config3);
        $this->assertEquals(22, $remote);
    }

    public function testGetRemoteCountWithMods()
    {
        $config4 = [
            'service_class' => ANakrutka::class,
            'order' => 4,
            'min' => 10,
            'max' => 100,
            'count_extra_percent' => 10,
            'count_min' => 10,
            'remote_extra_percent' => 100,
            'remote_params' => [
                'service' => 81,
            ],
            'net_cost' => [
                'amount' => 100,
                'local' => 4.187,
                'mode' => 'local',
            ],
        ];

        $local = AbstractService::getLocalCountWithMods(10, $config4);
        $this->assertEquals(11, $local);

        $remote = AbstractService::getRemoteCountWithMods(10, $config4);
        $this->assertEquals(22, $remote);
    }
}
