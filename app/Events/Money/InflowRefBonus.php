<?php

namespace App\Events\Money;

use App\Transaction;
use App\User;

class InflowRefBonus extends BaseTransaction
{
    public function __construct(User $user, $amount, $related, $comment = '')
    {
        parent::__construct($user, $amount, $comment);
        $this->type = Transaction::INFLOW_REF_BONUS;
        $this->related_user_id = $related->id;
    }
}
