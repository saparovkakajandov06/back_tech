<?php

namespace App\Observers;

use App\Services\EventsSchemesService;
use App\Transaction;
use App\User;

class TransactionObserver
{
    /**
     * Handle the Transaction "created" event.
     *
     * @param  \App\Transaction  $transaction
     * @return void
     */
    public function created(Transaction $transaction)
    {
        $userId = $transaction->getAttribute('user_id');
        $type = $transaction->getAttribute('type');
        $canSendMail = User::find($userId)->canSendMail();

        if($canSendMail and $type == 'OUTFLOW_ORDER'){
            $ess = resolve(EventsSchemesService::class);
            $ess->createSuccededPayment($transaction);
        }
    }

    /**
     * Handle the Transaction "updated" event.
     *
     * @param  \App\Transaction  $transaction
     * @return void
     */
    public function updated(Transaction $transaction)
    {
        //
    }

    /**
     * Handle the Transaction "deleted" event.
     *
     * @param  \App\Transaction  $transaction
     * @return void
     */
    public function deleted(Transaction $transaction)
    {
        //
    }

    /**
     * Handle the Transaction "restored" event.
     *
     * @param  \App\Transaction  $transaction
     * @return void
     */
    public function restored(Transaction $transaction)
    {
        //
    }

    /**
     * Handle the Transaction "force deleted" event.
     *
     * @param  \App\Transaction  $transaction
     * @return void
     */
    public function forceDeleted(Transaction $transaction)
    {
        //
    }
}