<?php

namespace App\Domain\OrderSM\States;

use App\Domain\OrderSM\IOrderState;
use App\Exceptions\Reportable\DistributorException;
use App\Order;
use Illuminate\Support\Facades\Log;

class CreatedState extends DefaultState implements IOrderState
{
    public function split()
    {
        $order = $this->order;

        try {
            $distribution = $order->userService->split($order);
            $order->params = array_merge($order->params, [
                'distribution' => $distribution
            ]);

            $this->order->update([ 'status' => Order::STATUS_SPLIT ]);

            $order->writeLog(__FUNCTION__, json_encode($distribution));

        } catch (\Throwable $e) {
            Log::info("Could not split order {$order->id} " . describe_exception($e));
            throw new DistributorException();
        }
    }
}
