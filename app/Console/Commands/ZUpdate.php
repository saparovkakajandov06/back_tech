<?php

namespace App\Console\Commands;

use App\Domain\Models\Chunk;
use App\Domain\Models\CompositeOrder;
use App\Order;
use App\Services\UpdateService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Throwable;

class ZUpdate extends Command
{
    protected $signature = 'z:update {action}';

    public UpdateService $svc;

    const PROCESS_ODD = 'odd';
    const PROCESS_EVEN = 'even';
    const PROCESS_RECENT = 'recent';

    public function __construct()
    {
        parent::__construct();
        $this->svc = new UpdateService();
    }

    public function log($msg)
    {
        $msg = '[Update] ' . $msg;
        Log::channel('orders')->info($msg);
    }

    private function monitor($name)
    {
        $pid = $this->svc->getProcessPid($name);
        if (!$pid || !file_exists("/proc/$pid")) {
            $cmd = "php artisan z:update $name";
            $process = Process::fromShellCommandline($cmd, timeout: 600);
            $process->disableOutput();
            $process->start();
        }
    }

    private function work(string $name)
    {
        $pid = getmypid();
        $this->svc->saveProcessPid($name, $pid);

        $start = microtime(true);

        $runningOrders = $this->svc->ordersForUpdate($name);
        $count = $runningOrders->count();

        $this->log("$name($pid): updating $count orders");

        $runningIds = $runningOrders->pluck('id');

        $chunks = Chunk::whereIn('composite_order_id', $runningIds)
            ->get()
            ->keyBy('id');

        $updateCount = 0;
        foreach($runningOrders as $order) {
            $order->xUpdateChunks($chunks);
            try {
                $orderFromDB = CompositeOrder::findOrFail($order->id);
                if (in_array($orderFromDB->status, [
                    Order::STATUS_COMPLETED,
                    Order::STATUS_PARTIAL_COMPLETED,
                    Order::STATUS_CANCELED,
                ])) {
                    $this->log("$name($pid) respects that order {$order->id} got status {$orderFromDB->status}");
                    $updateCount++;
                    continue;
                }
                $order->startUpdate(); // set updating state
                $order->nextState($chunks); // update done
                $updateCount++;
            }
            catch (Throwable $e) {
                $msg = "Could not set next state for order ";
                $msg .= $order->id . " ";
                $msg .= $e->getMessage();
                Log::stack(['daily', 'orders'])->error($msg);
            }
        }

        $updateTime = microtime(true) - $start;

        $this->log("$name($pid) finished. $updateCount orders in $updateTime sec");

        $this->svc->deleteProcessPid($name);
    }

    private function master()
    {
        if ($this->svc->isRunning()) {
            $this->log("Already started. Exit.");
            return;
        } elseif ($this->svc->isStopped()) {
            $this->log("Update stopped. Exit.");
            return;
        } else {
            $this->log("Starting.");
        }

        while(true) {
            if ($this->svc->isStopped()) {
                $this->log("Update was stopped. Exit.");
                return;
            }

            $this->svc->keepRunning();

            $this->monitor(self::PROCESS_ODD);
            $this->monitor(self::PROCESS_EVEN);
            $this->monitor(self::PROCESS_RECENT);

            $this->svc->sleep();
        }
    }

    private function start()
    {
        $this->svc->unstop();
    }

    private function stop()
    {
        $this->svc->stop();
    }

    public function handle()
    {
        $action = $this->argument('action');
        switch ($action) {
            case 'odd':
                $this->log("->$action");
                $this->work(self::PROCESS_ODD);
                break;
            case 'even':
                $this->log("->$action");
                $this->work(self::PROCESS_EVEN);
                break;
            case 'recent':
                $this->log("->$action");
                $this->work(self::PROCESS_RECENT);
                break;
            case 'master':
                $this->log("->$action");
                $this->master();
                break;
            case 'start':
                $this->log("->$action");
                $this->start();
                break;
            case 'stop':
                $this->log("->$action");
                $this->stop();
                break;
            default:
                $this->log("->$action");
                $this->log("Unknown action");
        }
    }
}
