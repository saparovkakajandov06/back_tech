<?php

namespace App\Domain\Models\Money;

use App\Transaction;
use App\User;

class MoneyBack extends BaseMoney
{
    protected int $precision = 2;

    public function applyTransaction(User|int $user): static
    {
        if (!$user instanceof User) {
            $user = User::findOrFail($user);
        }

        $val = $this->val();

        $this->moneyService->create(
            user: $user,
            type: $val >= 0 ? Transaction::INFLOW_REFUND : Transaction::OUTFLOW_CANCEL_REFUND,
            amount: $val,
            cur: $this->currency,
            comment: $this->comment,
            orderIds: $this->orderIds
        );

        return $this;
    }
}
