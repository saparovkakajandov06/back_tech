<?php

namespace App\Domain\Models\Money;

use App\Domain\Models\Money\Traits\Convertable;
use App\Transaction;
use App\User;
use Illuminate\Support\Facades\Log;

class RefBonus extends BaseMoney
{
    use Convertable;

    protected int $precision = 2;

    protected ?int $relatedUserId = null;

    public function __construct(
      $amount,
      $currency,
      $comment = null,
      $relatedUserId = null,
      $orderIds = [])
    {
        parent::__construct($amount, $currency, $comment, $orderIds);

        $this->relatedUserId = $relatedUserId;
    }

    public function applyTransaction(User|int $user): static
    {
        if (!$user instanceof User) {
            $user = User::findOrFail($user);
        }

        if ($this->currency !== $user->cur) {
            $this->convert($user->cur);
        }

        $val = $this->val();

        $this->moneyService->createWithRelated(
            $user,
            $val >= 0 ? Transaction::INFLOW_REF_BONUS :  Transaction::OUTFLOW_CANCEL_REF_BONUS,
            $val,
            $this->currency,
            $this->comment,
            $this->relatedUserId,
            $this->orderIds
        );
        return $this;
    }
}
