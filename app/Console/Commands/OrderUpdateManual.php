<?php

namespace App\Console\Commands;

use App\Domain\Models\Chunk;
use App\Domain\Models\CompositeOrder;
use App\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class OrderUpdateManual extends Command
{
    protected $signature = 'order_update:manual {ids*}';

    public function handle()
    {
        $ids = $this->argument('ids');
        $orders = CompositeOrder::whereIn('id', $ids)->get();
        $orderIds = $orders->pluck('id');
        $chunks = Chunk::whereIn('composite_order_id', $orderIds)
            ->get()
            ->keyBy('id');
        foreach($orders as $order) {
            $order->xUpdateChunks($chunks);
            try {
                $orderFromDB = CompositeOrder::findOrFail($order->id);
                if (in_array($orderFromDB->status, [
                    Order::STATUS_COMPLETED,
                    Order::STATUS_PARTIAL_COMPLETED,
                    Order::STATUS_CANCELED,
                ])) {
                    continue;
                }
                $order->startUpdate(); // set updating state
                $order->nextState($chunks); // update done
            }
            catch (Throwable $e) {
                $msg = "Could not set next state for order ";
                $msg .= $order->id . " ";
                $msg .= $e->getMessage();
                Log::stack(['daily', 'orders'])->error($msg);
            }
        }
    }
}
