<?php

namespace App\Domain\Services\Nakrutka;

use App\AddResult;
use App\Domain\Models\Chunk;
use App\ExternStatus;
use App\Order;

class NakrutkaFake extends ANakrutka
{
    public const CHARGE = 444.44;

    protected $count;

    public function getStatus($orderId): ExternStatus
    {
        return new ExternStatus(
            status: Order::STATUS_RUNNING,
            remains: $this->count,
            response: [
                "charge" => self::CHARGE,
                "start_count" => null,
                "status" => "In progress",
                "remains" => $this->count,
                "currency" => "RUB",
            ]);
    }

    public function add(Chunk $chunk, $orderParams, $svcConfig): AddResult
    {
        $this->count = $chunk->details['count'];
        $id = rand(0, 999999);

        $count = self::getLocalCountWithMods($this->count, $svcConfig);

        return new AddResult(
            request: array_merge([
                'key' => 'hidden',
                'action' => 'add',
                'link' => $chunk->details['link'],
                'quantity' => $count,
            ], $svcConfig['remote_params']), // service
            response: ['order' => $id],
            status: Order::STATUS_RUNNING,
            externId: $id,
            charge: $this->netCost($id, $count, $svcConfig),
        );
    }

    public function charge($orderId, $count, $svcConfig): float
    {
//        return 0.70588;
        return self::CHARGE;
    }
}
