<?php

namespace App\Domain\Services\Vkserfing;

use App\AddResult;
use App\Domain\Models\Chunk;
use App\ExternStatus;
use App\Order;

class VkserfingFake extends AVkserfing
{
    public const CHARGE = 111.11;

    protected $count;

    public function add(Chunk $chunk, $orderParams, $svcConfig): AddResult
    {
        $this->count = $this->count = $chunk->details['count'];
        $id = rand(0, 999999);

        $count = self::getLocalCountWithMods($this->count, $svcConfig);

        return new AddResult(
            request: array_merge([
                'token' => 'hidden',
                'link' => $chunk->details['link'],
                'status' => 'on',
                'amount_users_limit' => $this->count,
            ], $svcConfig['remote_params']), // type
            response: [
                'data' => [
                    'id' => $id,
                ],
                'status' => 'success',
            ],
            externId: $id,
            status: Order::STATUS_RUNNING,
            charge: $this->netCost($id, $count, $svcConfig),
        );
    }

    public function charge($orderId, $count, $svcConfig): float
    {
        return self::CHARGE;
    }

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
}
