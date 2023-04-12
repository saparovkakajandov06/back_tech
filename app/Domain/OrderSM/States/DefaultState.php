<?php

namespace App\Domain\OrderSM\States;

use App\Domain\Models\CompositeOrder;
use App\Domain\OrderSM\IOrderState;
use App\Exceptions\Reportable\NotImplementedException;

class DefaultState implements IOrderState
{
    public CompositeOrder $order;

    public function __construct(CompositeOrder $order)
    {
        $this->order = $order;
    }

    public function crazy()
    {
        $order = $this->order;

        $this->order->update([ 'status' => 'CRAZY' ]);
        $order->writeLog(__FUNCTION__, 'for testing');
    }

    public function split()
    {
        $method = static::class . '::' . __FUNCTION__;
        throw new NotImplementedException($method);
    }

    public function pay()
    {
        $method = static::class . '::' . __FUNCTION__;
        throw new NotImplementedException($method);
    }

    public function run()
    {
        $method = static::class . '::' . __FUNCTION__;
        throw new NotImplementedException($method);
    }

    public function startUpdate()
    {
        $method = static::class . '::' . __FUNCTION__;
        throw new NotImplementedException($method);
    }

    public function pause()
    {
        $method = static::class . '::' . __FUNCTION__;
        throw (new NotImplementedException($method))->withData([
            'order_id' => $this->order->id,
        ]);
    }

    public function complete()
    {
        $method = static::class . '::' . __FUNCTION__;
        throw (new NotImplementedException($method))->withData([
            'order_id' => $this->order->id,
        ]);
    }

    public function partial()
    {
        $method = static::class . '::' . __FUNCTION__;
        throw (new NotImplementedException($method))->withData([
            'order_id' => $this->order->id,
        ]);
    }

    public function cancel()
    {
        $method = static::class . '::' . __FUNCTION__;
        throw (new NotImplementedException($method))->withData([
            'order_id' => $this->order->id,
        ]);
    }

    public function error()
    {
        $method = static::class . '::' . __FUNCTION__;
        throw (new NotImplementedException($method))->withData([
            'order_id' => $this->order->id,
        ]);
    }

    public function modRun()
    {
        $method = static::class . '::' . __FUNCTION__;
        throw new NotImplementedException($method);
    }

    public function modStop()
    {
        $method = static::class . '::' . __FUNCTION__;
        throw new NotImplementedException($method);
    }

    public function modComplete()
    {
        $method = static::class . '::' . __FUNCTION__;
        throw new NotImplementedException($method);
    }

    public function modCancel()
    {
        $method = static::class . '::' . __FUNCTION__;
        throw new NotImplementedException($method);
    }

    public function modCompleteMain()
    {
        $method = static::class . '::' . __FUNCTION__;
        throw new NotImplementedException($method);
    }
}
