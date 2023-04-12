<?php

namespace App\Domain\Models\Money;

use App\Exceptions\Reportable\NotImplementedException;
use App\Services\Money\Services\TransactionsService;
use App\User;

class UserBalance extends BaseMoney implements Money
{
    protected int $precision = 2;

    protected int $mode = PHP_ROUND_HALF_UP;

    public static function forUser(User $user, string $cur=null): UserBalance
    {
        if (!$cur) {
            $cur = $user->cur;
        }
        $svc = resolve(TransactionsService::class);
        $amount = $svc->sum($user, $cur);

        return new UserBalance($amount, $cur);
    }

    public function applyTransaction(User|int $user): static
    {
        throw new NotImplementedException();
    }
}
