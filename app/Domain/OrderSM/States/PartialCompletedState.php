<?php

namespace App\Domain\OrderSM\States;


use App\Domain\OrderSM\IOrderState;
use App\Exceptions\Reportable\ModeratorActionException;
use App\Order;
use App\Services\InstagramNotificationService;

class PartialCompletedState extends DefaultState implements IOrderState
{
    public function modCompleteMain()
    {
        $order = $this->order;

        if (!$order->fromMain()) {
            throw new ModeratorActionException("Main only");
        }

        $completedPart = $order->getCompletedPart();

        // полная сумма реф бонуса - уже выплаченный бонус
        $order->giveRefBonus(1.0 - $completedPart);

        // отмена возврата за невыполненную часть
        $order->giveMoneyBack($completedPart - 1.0);

        $order->update(['status' => Order::STATUS_COMPLETED ]);

        app(InstagramNotificationService::class)->send($order);

        $order->writeLog(__FUNCTION__,
            "STATUS_PARTIAL_COMPLETED -> STATUS_COMPLETED");
    }
}
