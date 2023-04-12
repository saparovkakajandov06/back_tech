<?php

namespace App\Console\Commands;

use App\Domain\Models\CompositeOrder;
use App\Order;
use App\Q\Pool;
use App\Q\Slave;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class UpdateCommand extends Command
{
    protected $signature = 'ss:update {slaves} {timeout=30}';
    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function startLog()
    {
        $timestamp = Carbon::now()->toTimeString();

        $text = "[$timestamp] --- Update started ---\n";

        $fp = fopen('ss_update.log', 'a');
        fwrite($fp, $text);
        fclose($fp);
    }


    public function handle()
    {
        $startTime = microtime(true);

        $runningOrders = CompositeOrder::where('status', Order::STATUS_RUNNING)
            ->inRandomOrder()
//            ->orderBy('updated_at', 'asc')
            ->take(2000)
            ->get()
            ->all();

        $pool = new Pool();

        $n = $this->argument('slaves');
        $timeout = $this->argument('timeout');

        for ($i = 0; $i < $n; $i++) {
            $slave = new Slave($i);
            $slave->startTimer();
            $pool->addSlave($slave);
        }

        $this->startLog();

        foreach ($runningOrders as $order) {
            $slave = $pool->nextSlave();
            $slave->exitByTime($timeout);
//            echo "[UpdateCommand] Sending order {$order->id} to slave {$slave->id}\n";
            $slave->processOrder($order->id);
        }

        $pool->each(function($slave) {
           $slave->exit();
        });

        echo "[UpdateCommand] Update done\n";

//        $time = microtime(true) - $startTime;
//        $total = count($ss);

//        echo "Done $total orders in $time seconds\n";
    }
}