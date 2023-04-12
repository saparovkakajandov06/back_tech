<?php

namespace App\Domain\OrderSM\States;

use App\Domain\OrderSM\IOrderState;
use App\Order;

class SplitState extends DefaultState implements IOrderState
{
    public function pay()
    {
        $this->order->update([ 'status' => Order::STATUS_PAID ]);
        $this->order->writeLog(__FUNCTION__, "status changed, amount: ");

        return true;
    }
}
