<?php

namespace App\Console\Commands;

use App\Domain\Models\Chunk;
use App\Domain\Models\CompositeOrder;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoDelete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smm:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete old orders';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function log($msg)
    {
        $msg = '[Delete] ' . $msg;
        Log::channel('orders')->info($msg);
    }

    public function handle()
    {
        $ordersToDelete = CompositeOrder::
                        where('created_at', '<', Carbon::parse('24 hours ago'))
                        ->notPaid()
                        ->get();

        $count = 0;
        foreach($ordersToDelete as $order) {
            Chunk::where('composite_order_id', $order->id)->delete();
            $order->delete();
            $count++;
        }

        echo "Deleted $count orders with chunks\n";
        $this->log("Deleted $count orders with chunks");

//        $chunks = Chunk::whereNotIn('composite_order_id', CompositeOrder::all()
//                    ->map(fn($order)=>$order->id))
//                    ->delete();
//
//        echo "Deleted $chunks chunks\n";
    }
}
