<?php

namespace App\Listeners\Money;

use App\Events\Money\BaseTransaction;
use App\Transaction;

class RefBonus
{
    public function handle(BaseTransaction $event)
    {
        $transaction = Transaction::where('type', Transaction::OUTFLOW_ORDER)
                                  ->where('event_id', $event->id)
                                  ->firstOrFail();

        $parent = $event->user->parent;
        if ($parent) {

            $parent->transactions()->create([
                'type' => Transaction::INFLOW_REF_BONUS,
                'amount' => $transaction->amount * (-0.1),
                'comment' => 'бонус',
                'event_id' => $event->id,
                'related_user_id' => $transaction->user_id,
            ]);
        }
    }
}
