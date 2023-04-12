<?php

namespace App\Domain\OrderSM\States;

use App\Domain\OrderSM\IOrderState;
use App\Order;
use Illuminate\Support\Facades\Log;

class ErrorState extends DefaultState implements IOrderState
{
    public function modCancel()
    {
        $this->order->giveMoneyBack(1.0);

        $this->order->update([
            'status' => Order::STATUS_CANCELED
        ]);

        $this->order->writeLog(__FUNCTION__,
            "STATUS_ERROR -> STATUS_CANCELED");
    }

    public function modComplete()
    {
        $this->order->giveRefBonus(1.00);

        $this->order->update([
            'status' => Order::STATUS_COMPLETED
        ]);

        $this->order->writeLog(__FUNCTION__,
            "STATUS_ERROR -> STATUS_COMPLETED");
    }

    public function startUpdate()
    {
        $this->order->update(['status' => Order::STATUS_UPDATING]);
    }

    // restart
    public function modRun()
    {
        $order = $this->order;
        try {
            $restarted = 0;
            foreach ($order->chunks->where('status', Order::STATUS_ERROR) as $chunk) {
                try {
                    $addResult = $chunk->run();

                    if ($addResult->status === Order::STATUS_RUNNING) {
                        $restarted++;
                    } else { // error, canceled
                        Log::info("Chunk {$chunk->id} not started"
                            . json_encode($addResult));
                    }
                } catch (\Throwable $e) {
                    $msg = "Error while starting chunk {$chunk->id}: ";
                    $msg .= $e->getMessage();
                    $msg .= " file {$e->getFile()} line {$e->getLine()}";
                    Log::error($msg);

                } finally {
                    $order->writeLog(__FUNCTION__, json_encode([
                        'addResult' => $addResult ?? 'null',
                        'info' => 'restart attempt',
                    ]));
                }
            }

            if ($restarted > 0) {
                $order->ga();
                $order->metaPixel();

                $order->update([ 'status' => Order::STATUS_RUNNING ]);

                $order->writeLog(__FUNCTION__,
                    "Running. Restarted $restarted chunks");
            } else {
                $order->update(['status' => Order::STATUS_ERROR]);

                $order->writeLog(__FUNCTION__,
                    "Error. Not restarted");
            }

        } catch (\Throwable $e) {
            Log::info('Cannot change state to running '.$e->getMessage());
        }
    }

    // auto restart
    public function run()
    {
        $order = $this->order;
        try {
            $restarted = 0;
            foreach ($order->chunks->where('status', Order::STATUS_ERROR) as $chunk) {
                try {
                    $addResult = $chunk->run();

                    if ($addResult->status === Order::STATUS_RUNNING) {
                        Log::stack(['daily', 'orders'])
                            ->info("Chunk {$chunk->id} started ok");
                        $restarted++;
                    } else { // error, canceled
                        Log::stack(['daily', 'orders'])
                            ->info("Chunk {$chunk->id} not started"
                            . json_encode($addResult));
                    }
                } catch (\Throwable $e) {
                    $msg = "Error while starting chunk {$chunk->id}: ";
                    $msg .= $e->getMessage();
                    $msg .= " file {$e->getFile()} line {$e->getLine()}";
                    Log::stack(['daily', 'orders'])->error($msg);

                } finally {
                    $order->writeLog(__FUNCTION__, json_encode([
                        'addResult' => $addResult ?? 'null',
                        'info' => 'restart attempt',
                    ]));
                }
            }

            if ($restarted > 0) {
                // TODO: rethink ga call, we only need one:
                // when all chunks has extern_id
                $order->ga();
                $order->metaPixel();

                $order->update([ 'status' => Order::STATUS_RUNNING ]);

                $order->writeLog(__FUNCTION__,
                    "Running. Restarted $restarted chunks");

                Log::stack(['daily', 'orders'])
                    ->info("Order {$order->id} restarted ok.");

            } else {
                $order->update(['status' => Order::STATUS_ERROR]);

                $order->writeLog(__FUNCTION__, "Error. Not restarted");

                Log::stack(['daily', 'orders'])
                    ->info("Order {$order->id} not restarted.");
            }

        } catch (\Throwable $e) {
            Log::stack(['daily', 'orders'])
                ->info('Cannot change state to running '.$e->getMessage());
        }
    }
}
