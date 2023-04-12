<?php

namespace App\Domain\Services\Prm4uAuto;

use App\AddResult;
use App\Domain\Models\Chunk;
use App\ExternStatus;
use App\Order;

class Prm4uAutoFake extends APrm4uAuto
{
    public const CHARGE = 777.77;

    protected $count;

    public function getStatus($orderId): ExternStatus
    {
        return new ExternStatus(
            status: Order::STATUS_RUNNING,
            remains: $this->count,
            response: [
                'charge' => self::CHARGE,
                'start_count' => null,
                'status' => 'In progress',
                'remains' => $this->count,
                'currency' => 'USD',
            ]);
    }

    public function add(Chunk $chunk, $params, $svcConfig): AddResult
    {
        // модификаций нет, просто среднее * количество постов
        $this->count = $params['posts'] * avg($params['min'], $params['max']);
        $id = rand(0, 999999);

        $data = array_merge([
            'key' => 'hidden',
            'action' => 'add',
            'username' => $params['login'],
            'min' => $params['min'],
            'max' => $params['max'],
            'posts' => $params['posts'],
        ], $svcConfig['remote_params']); // service, delay

        return new AddResult(
            request: $data,
            response: ['order' => $id],
            externId: $id,
            status: Order::STATUS_RUNNING,
            charge: $this->netCost($id, $this->count, $svcConfig),
        );
    }

    public function charge($orderId, $count, $svcConfig): float
    {
        return self::CHARGE;
    }
}
