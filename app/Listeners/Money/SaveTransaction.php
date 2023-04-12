<?php

namespace App\Listeners\Money;

use App\Events\Money\BaseTransaction;

class SaveTransaction
{
    public function handle(BaseTransaction $event)
    {
        $event->user->transactions()->create([
            'type' => $event->type,
            'amount' => $event->amount,
            'comment' => $event->comment,
            'event_id' => $event->id,
            'related_user_id' => $event->related_user_id,
        ]);
    }
}
