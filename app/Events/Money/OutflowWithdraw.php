<?php

namespace App\Events\Money;

use App\Events\Money\BaseTransaction;
use App\Transaction;

class OutflowWithdraw extends BaseTransaction
{
    public $withdrawType;
    public $walletNum;

    public function __construct(
        $user,
        $withdrawType,
        $walletNum,
        $amount,
        $comment = ''
    ) {
        parent::__construct($user, $amount, $comment);
        $this->withdrawType = $withdrawType;
        $this->walletNum = $walletNum;
        $this->type = Transaction::OUTFLOW_WITHDRAWAL;
    }
}
