<?php

namespace App\Domain\Services\Vtope;

use App\AddResult;
use App\Domain\Models\Chunk;
use App\ExternStatus;
use App\Order;

class VtopeFake extends AVtope
{
    protected $count;

    public function add(Chunk $chunk, $params, $svcConfig): AddResult
    {
        $this->count = self::getLocalCountWithMods(
            $chunk->details['count'], $svcConfig);
        $id = rand(0, 999999);

        $data = array_merge([
            'user' => '123',
            'key' => 'hidden',
            'link' => $chunk->details['link'],
            'count' => $this->count,
        ], $svcConfig['remote_params']); // method, service, type

        return new AddResult(
            request: $data,
            response: [
                "errorcode" => 0,
                "id" => $id,
                "isnew" => false
            ],
            externId: $id,
            status: Order::STATUS_RUNNING,
        );
    }

    public function getStatus($orderId): ExternStatus
    {
        return new ExternStatus(
            status: Order::STATUS_RUNNING,
            remains: $this->count,
            response: [
                "count" => $this->count,
                "status" => "ok",
                "initcount" => 20,
                "errorcode" => 0,
                "starting_count" => 16,
            ]
        );
    }

    public function charge($orderId, $count, $svcConfig): float
    {
        return 0.;
    }
}
