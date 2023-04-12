<?php

namespace App\Domain\Models\Money;

use App\Transaction;
use App\User;

class OrderCost extends BaseMoney implements Money
{
    protected int $precision = 2;

    protected int $mode = PHP_ROUND_HALF_DOWN;

    public function __construct($amount, $currency, $comment = null)
    {
        if ($amount > 0) {
            $amount *= -1;
        }
        parent::__construct($amount, $currency, $comment);
    }

    public function applyTransaction(User|int $user)
    {
        if (!$user instanceof User) {
            $user = User::findOrFail($user);
        }

        return $this->moneyService->create(
            $user,
            Transaction::OUTFLOW_ORDER,
            $this->val(),
            $this->currency,
            $this->comment,
        );
    }
}
