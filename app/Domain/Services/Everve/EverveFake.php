<?php

namespace App\Domain\Services\Everve;

use App\AddResult;
use App\Domain\Models\Chunk;
use App\ExternStatus;
use App\Order;

class EverveFake extends AEverve
{
    public const CHARGE = 222.22;

    protected $count;

    public function getStatus($orderId): ExternStatus
    {
        return new ExternStatus(
            externId: $orderId,
            status: Order::STATUS_RUNNING,
            completed: 0,
            response: [
                'id' => $orderId,
                'status' => 'on',
                'link' => 'http://everve.fake.link',
            ]);
    }

    public function add(Chunk $chunk, $orderParams, $svcConfig): AddResult
    {
        $count = self::getLocalCountWithMods($chunk->details['count'], $svcConfig);
        $id = rand(0, 999999);

        $data = array_merge([
            'api_key' => 'hidden',
            'order_url' => $orderParams['link'],
            'order_overall_limit' => $count,
            'clear_prev_stat' => 1,
        ], $svcConfig['remote_params']); // category_id, order_price

        return new AddResult(
            request: $data,
            response: ['id' => $id],
            status: Order::STATUS_RUNNING,
            externId: $id,
            charge: $this->netCost($id, $count, $svcConfig),
        );
    }

    public function charge(int $orderId, $count, $svcConfig): float
    {
        return self::CHARGE;
    }
}
