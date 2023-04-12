<?php

namespace App\Console\Commands;

use App\Domain\Models\CompositeOrder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Export extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smm:export {lim}';

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
     * @return int
     */
    public function handle()
    {
        $lim = $this->argument('lim');
        echo "export $lim\n";

//        $orders = CompositeOrder::limit($lim)->get();

        $fp = fopen('co.json', 'a');

        $text = "";

//        foreach($orders as $order) {
//            $text .= $order->toJson();
//        }

        $orders = DB::table('composite_orders')->limit($lim)->get();
        foreach($orders as $order) {
            $order->params = json_decode($order->params);
            $text .= json_encode($order);
            $text .= "\n";
        }

        echo "writing...\n";

        fwrite($fp, $text);

        fclose($fp);

        echo "done\n";
        return 0;
    }
}
