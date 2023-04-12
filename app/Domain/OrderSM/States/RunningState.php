<?php

namespace App\Domain\OrderSM\States;

use App\Domain\OrderSM\IOrderState;
use App\Order;
use App\Services\InstagramNotificationService;

class RunningState extends DefaultState implements IOrderState
{
    public function modComplete()
    {
        $this->order->giveRefBonus(1.00);

        $this->order->update([ 'status' => Order::STATUS_COMPLETED ]);

        app(InstagramNotificationService::class)->send($this->order);

        $this->order->writeLog(__FUNCTION__,
            "STATUS_RUNNING -> STATUS_COMPLETED");
    }

    public function modStop()
    {
        $order = $this->order;
        $completedPart = $order->getCompletedPart();

        if($completedPart > 0) {
            // реф бонус за выполненную часть
            $order->giveRefBonus($completedPart);
            // возврат за невыполненную часть
            $order->giveMoneyBack(1.0 - $completedPart);

            $order->update([ 'status' => Order::STATUS_PARTIAL_COMPLETED ]);
            $order->writeLog(__FUNCTION__,
                "STATUS_RUNNING -> STATUS_PARTIAL_COMPLETED");
        } else {
            // полный возврат
            $order->giveMoneyBack(1.0);

            $order->update([ 'status' => Order::STATUS_CANCELED ]);
            $order->writeLog(__FUNCTION__,
                "STATUS_RUNNING -> STATUS_CANCELED");
        }
    }

    public function startUpdate()
    {
        $this->order->update(['status' => Order::STATUS_UPDATING]);
    }
}
