<?php

namespace App\Console\Commands;

use App\Payment;
use App\Domain\Models\CompositeOrder;
use Illuminate\Console\Command;

class CopyIpData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smm:CopyIpData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy ip data from CompositeOrders to Payments';

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
        Payment::whereNull('ip')->orderBy('id', 'ASC')->lazy()->each(function ($payment) {
            if(! $payment->order_ids || !$order = CompositeOrder::whereIn('id', $payment->order_ids)->first()) {
                return;
            }
            $payment->ip = $order->params['ip'];
            $payment->geocode = $order->params['country'];
            $payment->save();
        });

        return 0;
    }
}
