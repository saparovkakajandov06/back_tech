<?php

namespace App\Domain\OrderSM\States;


use App\Domain\OrderSM\IOrderState;
use App\Order;

class PausedState extends DefaultState implements IOrderState
{
    public function startUpdate()
    {
        $this->order->update(['status' => Order::STATUS_UPDATING]);
    }
}
