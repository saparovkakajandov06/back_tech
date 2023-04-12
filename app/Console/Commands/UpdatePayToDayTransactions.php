<?php

namespace App\Console\Commands;

use App\Payment;
use App\PaymentSystems\PayToDayPaymentSystem;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Output\ConsoleOutput;

class UpdatePayToDayTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'psp:updatePayToDayTransactions';

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
    public function handle(PayToDayPaymentSystem $paymentSystem)
    {
        $output = new ConsoleOutput();

        $output->writeln('Run updatePayToDayTransactions ... ');

        $data = Payment::where('payment_system', PayToDayPaymentSystem::class)
            ->whereIn('status', ['pending', 'new'])
            ->get();

        if (config('payment-systems.isDebugMode')) {
            Log::channel('payments')->debug('Check pay to day payments', [
                'payments' => $data
            ]);
        }

        $output->writeln(sprintf('Check pay to day payments. Count: [%s]', count($data)));

        foreach ($data as $transaction) {
            try {
                $orderId = $transaction->foreign_id;

                $output->writeln("Check order: [$orderId] ...");

                $order = $paymentSystem->getOrder($orderId);

                if ($order === false)
                    continue;

                $output->writeln("Update an order");
                $output->writeln(json_encode($order));

                $paymentSystem->handleUpdatePayToDate(new Request, $order);
            } catch (\Exception $e) {
                Log::channel('payments')->info('[error] PayToDay ' . json_encode($e), [
                    'orderId' => $orderId,
                    'tx' => $transaction
                ]);

                $output->writeln("Order update error");
            }
        }
    }
}
