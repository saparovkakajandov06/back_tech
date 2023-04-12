<?php

namespace App\Events\Money;

use App\Transaction;
use App\User;

class OutflowOrder extends BaseTransaction
{
    public function __construct(User $user, $amount, $comment = '')
    {
        parent::__construct($user, $amount, $comment);
        $this->type = Transaction::OUTFLOW_ORDER;
    }
}
