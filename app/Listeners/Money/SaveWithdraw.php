<?php

namespace App\Listeners\Money;

use App\Events\Money\OutflowWithdraw;
use App\Transaction;
use App\Withdraw;

class SaveWithdraw
{
    public function handle(OutflowWithdraw $event): void
    {
        $transaction = Transaction::where('event_id', $event->id)
                                  ->firstOrFail();

        Withdraw::create([
            'transaction_id' => $transaction->id,
            'event_id' => $event->id,
            'type' => $event->withdrawType,
            'wallet_number' => $event->walletNum,
        ]);
    }
}
