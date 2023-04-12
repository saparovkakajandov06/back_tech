<?php

namespace App\Domain\Services;

use App\AddResult;
use App\Domain\Models\Chunk;
use App\ExternStatus;
use App\Order;

class TestService1 extends ATestService1
{
    public function add(Chunk $chunk, $params, $config): AddResult
    {
        return new AddResult(status: Order::STATUS_RUNNING);
    }

    public function charge($orderId, $count, $svcConfig): float
    {
        return 0.;
    }

    public function getStatus(int $orderId): ExternStatus
    {
        return new ExternStatus();
    }
}
