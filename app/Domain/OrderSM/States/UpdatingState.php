<?php

namespace App\Domain\OrderSM\States;

use App\Domain\OrderSM\IOrderState;
use App\Exceptions\Reportable\ModeratorActionException;
use App\Order;
use App\Services\InstagramNotificationService;
use Illuminate\Support\Carbon;

class UpdatingState extends DefaultState implements IOrderState
{
    public function startUpdate()
    {
        // noop
    }

    public function run()
    {
        $this->order->update(['status' => Order::STATUS_RUNNING]);
    }

    public function complete()
    {
        $this->order->giveRefBonus(1.00);

        $this->order->update([ 'status' => Order::STATUS_COMPLETED ]);
        app(InstagramNotificationService::class)->send($this->order);
        $this->order->writeLog(__FUNCTION__,
            "STATUS_UPDATING -> STATUS_COMPLETED");
    }

    public function partial()
    {
        $order = $this->order;

        $completedPart = $order->getCompletedPart();

        // реф бонус за выполненную часть
        $order->giveRefBonus($completedPart);

        // возврат за невыполненную часть
        $order->giveMoneyBack(1.0 - $completedPart);

        $order->update([ 'status' => Order::STATUS_PARTIAL_COMPLETED ]);

        $order->writeLog(__FUNCTION__,
            "STATUS_UPDATING -> STATUS_PARTIAL_COMPLETED");
    }

    public function cancel()
    {
        // полный возврат
        $this->order->giveMoneyBack(1.0);

        $this->order->update([
            'status' => Order::STATUS_CANCELED
        ]);

        $this->order->writeLog(__FUNCTION__,
            "STATUS_UPDATING -> STATUS_CANCELED");
    }

    public function pause()
    {
        $this->order->update(['status' => Order::STATUS_PAUSED]);

        $this->order->writeLog(__FUNCTION__,
            "STATUS_UPDATING -> STATUS_PAUSED");
    }

    public function error()
    {
        $this->order->update([ 'status' => Order::STATUS_ERROR ]);

        $this->order->writeLog(__FUNCTION__,
            "STATUS_UPDATING -> STATUS_ERROR");
    }

    public function modRun()
    {
        $current = Carbon::now();
        if($current->diffInSeconds($this->order->updated_at) > 600){
            $this->order->update([ 'status' => Order::STATUS_RUNNING ]);
            $this->order->writeLog(__FUNCTION__,
                "STATUS_UPDATING -> STATUS_RUNNING");
        } else{
            throw new ModeratorActionException(__('exceptions.please_wait'));
        }
    }
}
