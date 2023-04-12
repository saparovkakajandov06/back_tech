<?php

namespace App\Domain\OrderSM\States;

use App\Domain\OrderSM\IOrderState;
use App\Exceptions\Reportable\ModeratorActionException;
use App\Order;

class CanceledState extends DefaultState implements IOrderState
{
    public function modCompleteMain()
    {
        $order = $this->order;

        if (!$order->fromMain()) {
            throw new ModeratorActionException("Main only");
        }

        $order->giveRefBonus(1.0);

        $order->update([
            'status' => Order::STATUS_COMPLETED
        ]);

        $order->writeLog(__FUNCTION__,
            "{$order->status} -> STATUS_COMPLETED");
    }
}
