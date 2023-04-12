<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;
use App\Payment;
use App\PaymentSystems\ConnectumPaymentSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UpdateConnectumTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updateConnectumTransactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(ConnectumPaymentSystem $paymentSystem)
    {
        $data = Payment::where('payment_system', ConnectumPaymentSystem::class)
            ->whereIn('status', ['pending', 'new', 'prepared'])
            ->where('created_at', '>=', Carbon::now()->subHours(1)->toDateTimeString()) //check timezone!
            ->get();

        foreach ($data as $transaction) {
            try {
                $order_id = $transaction->foreign_id;
                $order = $paymentSystem->getOrder($order_id);
                $paymentSystem->handleHookConnectum(new Request, $order);
            }
            catch (\Exception $e) {
                Log::channel('orders')->info('[error] connectum ' . json_encode($e));
            }
        }
    }
}
