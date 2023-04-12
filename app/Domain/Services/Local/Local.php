<?php

namespace App\Domain\Services\Local;

use App\Action;
use App\AddResult;
use App\Domain\Models\Chunk;
use App\ExternStatus;
use App\Order;

class Local extends ALocal
{
    public function add(Chunk $chunk, $orderParams, $svcConfig): AddResult {
        $res = new AddResult(status: Order::STATUS_RUNNING, externId: $chunk->id);

        //            ->setCharge(0); //todo fix

        return $res;
    }

    public function charge($orderId, $count, $svcConfig): float
    {
        return 0.;
    }

    public function getStatus(int $orderId): ExternStatus
    {
        $actions = Action::where('chunk_id', $orderId)
                           ->count();
        $completed = Action::where('chunk_id', $orderId)
                           ->where('completed', 1)
                           ->count();

        $status = $actions == $completed ? Order::STATUS_COMPLETED
                                         : Order::STATUS_RUNNING;

        return new ExternStatus(status: $status, remains: $actions - $completed);
    }
}
