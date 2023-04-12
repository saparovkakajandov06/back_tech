<?php

namespace App\Domain\OrderSM\States;

use App\Domain\OrderSM\IOrderState;
use App\Order;
use App\Services\InstagramNotificationService;
use Illuminate\Support\Facades\Log;

class PaidState extends DefaultState implements IOrderState
{
    public function modRun()
    {
        $this->doRun(__FUNCTION__);
    }

    public function run()
    {
        $this->doRun(__FUNCTION__);
    }

    protected function doRun(string $function)
    {
        $order = $this->order;

        try {
            $started = 0;
            foreach ($order->chunks as $chunk) {
                try {
                    $addResult = $chunk->run();

                    if ($addResult->status === Order::STATUS_RUNNING) {
                        $started++;
                    } else { // error, canceled
                        Log::info("Chunk {$chunk->id} not started " . json_encode($addResult));
                    }
                } catch (\Throwable $e) {
                    $msg = "Error while starting chunk {$chunk->id}: ";
                    $msg .= $e->getMessage();
                    $msg .= " file {$e->getFile()} line {$e->getLine()}";
                    Log::error($msg);
                } finally {
                    /*
                     * // fixme Убрать комментарий после отладки
                     * Возможны ситуации при которых не меняется статус:
                     * 1) $addResult не определена, тогда статус не сменяется и падаем в исключение "Undefined variable $addResult"; [fixed]
                     * 2) "Array to string conversion"
                     * 3) syntax error, unexpected token "?", expecting "->" or "?->" or "{" or "["
                     */
                    $order->writeLog($function, json_encode($addResult ?? []));
                }
            }

            if ($started > 0) {
                $order->update(['status' => Order::STATUS_RUNNING]);
                app(InstagramNotificationService::class)->send($order);
                $order->writeLog($function, "Set STATUS_RUNNING");
                $order->ga();
                $order->metaPixel();
            } else {
                $order->update(['status' => Order::STATUS_ERROR]);
                $order->writeLog($function, "Set STATUS_ERROR");
            }
        } catch (\Throwable $e) {
            Log::info('Cannot change state to running ' . $e->getMessage());
            Log::error("{$e->getMessage()} file {$e->getFile()} line {$e->getLine()}");
        }
    }
}
