<?php

namespace App\Console\Commands;

use App\Domain\Models\CompositeOrder;
use App\Order;
use Illuminate\Console\Command;

class Track extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smm:track';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $orders = CompositeOrder::whereIn('status', [Order::STATUS_RUNNING])->get();
        foreach($orders as $order) {
            if(! empty($order->userService->tracker)) {
                $tracker = new $order->userService->tracker;
                $orderParams = $order->params;
                $value = $tracker->getValue($order->params);
                $orderParams['tracker_now'] = $value;
                $order->params = $orderParams;
                $order->save();
                echo "order {$order->id} tracker value {$value}\n";
            }
        }
    }
}
