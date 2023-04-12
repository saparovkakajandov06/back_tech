<?php

namespace App\Console\Commands;

use App\Order;
use App\Services\NakrutkaService;
use Illuminate\Console\Command;

class GetStatuses extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smm:get_statuses {--save}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get statuses for active orders';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle() {
        $runningOrders = Order::where('status', Order::STATUS_RUNNING)->get()->all();

        if (empty($runningOrders)) {
            echo "No running orders\n";
            return;
        }

        $req = [];

        foreach($runningOrders as $order) {
            $req[] = $order->getForeignId();//add nakrutka ids to request
        }

        $nakrutka = app()->make(NakrutkaService::class);
        $res = $nakrutka->multiStatus($req);

        foreach($runningOrders as $order) {
            $nid = $order->details['nakrutka_id'];
            $n_status = $res->$nid->status;
            echo "order $order->id $order->uuid $nid $order->status $n_status\n";

            if ($this->option('save')) {
                $order->changeStatus($res);
            }
        }

        // In progress - выполняется
        // Pending - ожидает
        // Processing -

        // Partial - частично выполнен. возврат.

        // Canceled - отменен

        // Completed - выполнен

        print_r($res);
    }
}
